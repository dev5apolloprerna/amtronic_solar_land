<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubLeaseRequest;
use App\Models\Lease;
use App\Models\Park;
use App\Models\Party;
use App\Models\SubLease;
use App\Services\CapacityService;
use App\Services\DocumentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** MW sub-leases carved out of a parent lease. Overlapping active MW can never exceed the lease's MW. */
class SubLeaseController extends Controller
{
    public function __construct(private CapacityService $capacity, private DocumentService $documents) {}

    public function index(Request $request)
    {
        $q = SubLease::visibleTo()->with(['lease.plot', 'party', 'park', 'spv'])
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('sub_lease_no', 'like', "%$s%")->orWhere('agreement_no', 'like', "%$s%")
                ->orWhereHas('party', fn ($p) => $p->where('party_name', 'like', "%$s%"))
                ->orWhereHas('lease', fn ($l) => $l->where('lease_no', 'like', "%$s%"))))
            ->when($request->park_id, fn ($q, $v) => $q->where('park_id', $v))
            ->when($request->lease_id, fn ($q, $v) => $q->where('lease_id', $v))
            ->when($request->party_id, fn ($q, $v) => $q->where('party_id', $v))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->from, fn ($q, $v) => $q->whereDate('end_date', '>=', $v))
            ->when($request->to, fn ($q, $v) => $q->whereDate('start_date', '<=', $v))
            ->latest('start_date');

        if ($export = $this->maybeExport('sublease', 'Sub-Lease Register', ['Sub-lease no.', 'Parent lease', 'Land ref no.', 'Park', 'SPV', 'Sub-lessee', 'Capacity MW', 'Lease MW', 'Start', 'End',
            'Rent', 'Frequency', 'Agreement no.', 'Lender NOC', 'Status'],
            $q->get()->map(fn ($s) => [$s->sub_lease_no, $s->lease->lease_no, $s->lease->plot->land_ref_no, $s->park->park_code, $s->spv->spv_code, $s->party->party_name, (float) $s->capacity_mw,
                (float) $s->lease->leased_capacity_mw, $s->start_date->format('Y-m-d'), $s->end_date->format('Y-m-d'), (float) $s->rent_amount, $s->rent_frequency, $s->agreement_no,
                $s->lender_noc_obtained, $s->status]))) {
            return $export;
        }

        return view('subleases.index', [
            'rows' => $q->paginate(config('solar.per_page'))->withQueryString(),
            'total' => SubLease::visibleTo()->count(),
            'parks' => Park::orderBy('park_code')->pluck('park_code', 'id'),
            'leases' => Lease::visibleTo()->orderBy('lease_no')->pluck('lease_no', 'id'),
            'parties' => Party::whereIn('party_type', ['Sub-lessee', 'Company', 'Other'])->orderBy('party_name')->pluck('party_name', 'id'),
        ]);
    }

    public function create(Request $request)
    {
        $lease = $request->lease_id ? Lease::visibleTo()->find($request->lease_id) : null;
        $sub = new SubLease(['lease_id' => $lease?->id, 'status' => 'Active', 'rent_frequency' => 'Annual', 'lender_noc_obtained' => 'No',
            'start_date' => $lease && $lease->lease_start_date->isFuture() ? $lease->lease_start_date : now(), 'end_date' => $lease?->lease_end_date]);

        return $this->drawer('Add sub-lease', 'SUB_LEASES', 'subleases.form', $this->formData($sub, $lease), module: 'sublease');
    }

    public function store(SubLeaseRequest $request)
    {
        $data = $request->validated();
        $sub = DB::transaction(function () use ($data, $request) {
            $lease = Lease::whereKey($data['lease_id'])->lockForUpdate()->firstOrFail(); // serialises concurrent sub-leases on one lease
            $sub = new SubLease(collect($data)->except('agreement_document')->all());
            $sub->park_id = $lease->park_id;
            $sub->spv_id = $lease->spv_id;
            $sub->sub_lease_no = SubLease::nextNumber();
            if ($sub->status === 'Active') {
                $this->capacity->assertSubLeaseFits($lease, (float) $sub->capacity_mw, Carbon::parse($sub->start_date), Carbon::parse($sub->end_date));
            }
            $sub->save();
            $this->afterSave($sub, $lease, $request);

            return $sub;
        });
        $lease = $sub->lease;

        return $this->ok("Sub-lease {$sub->sub_lease_no} saved", null,
            fmt_mw($this->capacity->remainingToday($lease)).' of '.$lease->lease_no.' still free today');
    }

    public function edit(SubLease $sublease)
    {
        abort_unless(auth()->user()->canAccessSpv($sublease->spv_id), 403);
        $ro = ! $this->canEdit('sublease');

        return $this->drawer(($ro ? 'View' : 'Edit').' sub-lease', 'SUB_LEASES · '.$sublease->sub_lease_no, 'subleases.form', $this->formData($sublease, $sublease->lease), $ro, 'sublease');
    }

    public function update(SubLeaseRequest $request, SubLease $sublease)
    {
        abort_unless(auth()->user()->canAccessSpv($sublease->spv_id), 403);
        $data = $request->validated();
        DB::transaction(function () use ($data, $sublease, $request) {
            $lease = Lease::whereKey($sublease->lease_id)->lockForUpdate()->firstOrFail();
            $sublease->fill(collect($data)->except('agreement_document')->all());
            if ($sublease->status === 'Active') {
                $this->capacity->assertSubLeaseFits($lease, (float) $sublease->capacity_mw, Carbon::parse($sublease->start_date), Carbon::parse($sublease->end_date), $sublease->id);
            }
            $sublease->save();
            $this->afterSave($sublease, $lease, $request);
        });

        return $this->ok("Sub-lease {$sublease->sub_lease_no} saved", null, fmt_mw($this->capacity->remainingToday($sublease->lease)).' of '.$sublease->lease->lease_no.' still free today');
    }

    public function destroy(SubLease $sublease)
    {
        abort_unless(auth()->user()->canAccessSpv($sublease->spv_id), 403);
        if ($sublease->status === 'Active') {
            return $this->refuse("{$sublease->sub_lease_no} is active. Set it to Cancelled first; cancelled sub-leases free their MW and can then be deleted.");
        }
        $sublease->delete();

        return $this->ok("Sub-lease {$sublease->sub_lease_no} deleted");
    }

    private function afterSave(SubLease $sub, Lease $lease, Request $request): void
    {
        $plot = $lease->plot;
        $hasActive = SubLease::where('lease_id', $lease->id)->where('status', 'Active')->exists();
        if ($hasActive && $plot->land_status === 'Leased') {
            $plot->update(['land_status' => 'Sub-leased']);
        } elseif (! $hasActive && $plot->land_status === 'Sub-leased') {
            $plot->update(['land_status' => 'Leased']);
        }
        if ($request->hasFile('agreement_document')) {
            $this->documents->store($request->file('agreement_document'), [
                'plot_id' => $lease->plot_id, 'documentable_type' => SubLease::class, 'documentable_id' => $sub->id,
                'doc_type' => 'Sub-lease deed', 'title' => 'Sub-lease deed '.($sub->agreement_no ?: $sub->sub_lease_no), 'is_original_scan' => 'Yes',
            ]);
        }
    }

    private function formData(SubLease $sub, ?Lease $lease): array
    {
        return [
            'sub' => $sub,
            'lease' => $lease,
            'leaseOpts' => $sub->exists ? [] : Lease::visibleTo()->where('status', 'Active')->with('plot')->orderBy('lease_no')->get()
                ->mapWithKeys(fn ($l) => [$l->id => "$l->lease_no · {$l->plot->land_ref_no} · ".fmt_mw($l->leased_capacity_mw)])->all(),
            'parties' => Party::whereIn('party_type', ['Sub-lessee', 'Company', 'Other'])->where(fn ($q) => $q->where('status', 'Active')->orWhere('id', $sub->party_id))
                ->orderBy('party_name')->get()->mapWithKeys(fn ($p) => [$p->id => "$p->party_name · $p->party_type · $p->party_code"])->all(),
            'agreements' => $sub->exists ? $sub->documents()->latest()->get() : collect(),
        ];
    }
}
