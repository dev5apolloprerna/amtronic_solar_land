<?php

namespace App\Http\Controllers;

use App\Enums\LandUnit;
use App\Http\Requests\LeaseRequest;
use App\Models\Lease;
use App\Models\Park;
use App\Models\Plot;
use App\Models\RentPayment;
use App\Models\Spv;
use App\Services\CapacityService;
use App\Services\DocumentService;
use App\Services\RentScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LeaseController extends Controller
{
    public function __construct(private CapacityService $capacity, private RentScheduleService $schedules, private DocumentService $documents) {}

    public function index(Request $request)
    {
        $q = Lease::visibleTo()->with(['plot.currentOwnership.shares.party', 'park', 'spv'])
            ->withSum(['subLeases as sub_active_mw' => fn ($s) => $s->where('status', 'Active')->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now())], 'capacity_mw')
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('lease_no', 'like', "%$s%")->orWhere('agreement_no', 'like', "%$s%")
                ->orWhereHas('plot', fn ($p) => $p->where('land_ref_no', 'like', "%$s%"))))
            ->when($request->park_id, fn ($q, $v) => $q->where('park_id', $v))
            ->when($request->spv_id, fn ($q, $v) => $q->where('spv_id', $v))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->from, fn ($q, $v) => $q->whereDate('lease_start_date', '>=', $v))
            ->when($request->to, fn ($q, $v) => $q->whereDate('lease_start_date', '<=', $v))
            ->latest('lease_start_date');

        if ($export = $this->maybeExport('lease', 'Lease Register', ['Lease no.', 'Land ref no.', 'Park', 'SPV', 'Lessors', 'Start', 'End', 'Months', 'Area (ac)', 'Leased MW',
            'Sub-leased MW today', 'Rent type', 'Rate', 'Annual rent', 'Frequency', 'Escalation', 'Security deposit', 'Agreement no.', 'Status'],
            $q->get()->map(fn ($l) => [$l->lease_no, $l->plot->land_ref_no, $l->park->park_code, $l->spv->spv_code,
                $l->plot->currentOwnership?->shares->map(fn ($s) => $s->party->party_name)->implode(', '), $l->lease_start_date->format('Y-m-d'), $l->lease_end_date->format('Y-m-d'),
                $l->lease_duration_months, (float) $l->lease_area_acres, (float) $l->leased_capacity_mw, (float) $l->sub_active_mw, $l->rent_type, (float) $l->rent_rate,
                (float) $l->annual_rent, $l->payment_frequency, fmt_num($l->escalation_pct).'% / '.$l->escalation_every_yrs.' yrs', (float) $l->security_deposit, $l->agreement_no, $l->status]))) {
            return $export;
        }

        return view('leases.index', [
            'rows' => $q->paginate(config('solar.per_page'))->withQueryString(),
            'total' => Lease::visibleTo()->count(),
            'parks' => Park::orderBy('park_code')->pluck('park_code', 'id'),
            'spvs' => Spv::visibleTo()->orderBy('spv_code')->pluck('spv_code', 'id'),
        ]);
    }

    public function create(Request $request)
    {
        $plot = $request->plot_id ? Plot::visibleTo()->find($request->plot_id) : null;
        $lease = new Lease([
            'plot_id' => $plot?->id, 'status' => 'Active', 'lease_duration_months' => 348, 'payment_frequency' => 'Annual', 'rent_type' => 'Per acre',
            'land_unit' => 'Acre', 'total_lease_area' => $plot?->area_under_lease_acres, 'rent_rate' => $plot?->rate_per_acre,
            'escalation_pct' => $plot?->increment_pct ?? 5, 'escalation_every_yrs' => $plot?->increment_after_yrs ?? 3,
            'tds_applicable' => 'Yes', 'tds_rate' => 10, 'gst_applicable' => 'No', 'security_deposit' => 0, 'leased_capacity_mw' => $plot?->grid?->connectivity_mw,
        ]);

        return $this->drawer('Add lease', 'LEASES', 'leases.form', $this->formData($lease), module: 'lease');
    }

    public function store(LeaseRequest $request)
    {
        $data = $request->validated();
        $lease = DB::transaction(function () use ($request, $data) {
            $plot = Plot::with('park')->findOrFail($data['plot_id']);
            [$start, $end] = $request->term();
            $lease = new Lease(collect($data)->except('agreement_document')->all());
            $this->derive($lease, $plot, $end);
            $lease->lease_no = Lease::nextNumber();
            if ($lease->status === 'Active') {
                Park::whereKey($plot->park_id)->lockForUpdate()->first();
                $this->capacity->assertLeaseFits($plot->park, (float) $lease->leased_capacity_mw, $start, $end);
            }
            $lease->save();
            if (in_array($plot->land_status, ['Identified', 'ATL signed'], true) && $lease->status === 'Active') {
                $plot->update(['land_status' => 'Leased']);
            }
            $this->attachAgreement($request, $lease);

            return $lease;
        });
        $n = $this->schedules->generate($lease);

        return $this->ok("Lease {$lease->lease_no} saved".($n ? " · $n rent instalments generated" : ''));
    }

    public function edit(Lease $lease)
    {
        abort_unless(auth()->user()->canAccessSpv($lease->spv_id), 403);
        $ro = ! $this->canEdit('lease');

        return $this->drawer(($ro ? 'View' : 'Edit').' lease', 'LEASES · '.$lease->lease_no, 'leases.form', $this->formData($lease), $ro, 'lease');
    }

    public function update(LeaseRequest $request, Lease $lease)
    {
        abort_unless(auth()->user()->canAccessSpv($lease->spv_id), 403);
        $data = $request->validated();
        DB::transaction(function () use ($request, $data, $lease) {
            Lease::whereKey($lease->id)->lockForUpdate()->first();
            [$start, $end] = $request->term();
            $lease->fill(collect($data)->except('agreement_document')->all());
            $this->derive($lease, $lease->plot, $end);
            $minMw = $this->capacity->minimumLeaseMw($lease, $start, $end);
            if ((float) $lease->leased_capacity_mw + CapacityService::EPS < $minMw) {
                throw ValidationException::withMessages(['leased_capacity_mw' => 'Active sub-leases already use '.CapacityService::fmt($minMw).' MW at peak. Leased capacity cannot go below that.']);
            }
            if ($lease->status === 'Active') {
                Park::whereKey($lease->park_id)->lockForUpdate()->first();
                $this->capacity->assertLeaseFits($lease->park, (float) $lease->leased_capacity_mw, $start, $end, $lease->id);
            }
            $lease->save();
            $this->attachAgreement($request, $lease);
        });
        $msg = "Lease {$lease->lease_no} saved";
        if (RentScheduleService::termsChanged($lease)) {
            $n = $this->schedules->generate($lease);
            $msg .= " · $n unpaid instalments regenerated";
        }

        return $this->ok($msg);
    }

    public function destroy(Lease $lease)
    {
        abort_unless(auth()->user()->canAccessSpv($lease->spv_id), 403);
        if ($lease->subLeases()->withTrashed()->exists()) {
            return $this->refuse("{$lease->lease_no} has sub-leases. Delete them first.");
        }
        if (RentPayment::whereHas('allocation.schedule', fn ($q) => $q->where('lease_id', $lease->id))->exists()) {
            return $this->refuse("{$lease->lease_no} has recorded rent payments and cannot be deleted. Set its status to Terminated instead.");
        }
        DB::transaction(function () use ($lease) {
            $lease->schedules()->get()->each->delete();
            $lease->delete();
        });

        return $this->ok("Lease {$lease->lease_no} deleted");
    }

    /** Derived columns: park, SPV, end date, acres, annual rent. */
    private function derive(Lease $lease, Plot $plot, $end): void
    {
        $lease->plot_id = $plot->id;
        $lease->park_id = $plot->park_id;
        $lease->spv_id = $plot->spv_id;
        $lease->lease_end_date = $end;
        $lease->lease_area_acres = LandUnit::acres($lease->land_unit, (float) $lease->total_lease_area);
        $lease->annual_rent = RentScheduleService::annualRent($lease->rent_type, (float) $lease->rent_rate, (float) $lease->lease_area_acres);
        $lease->security_deposit ??= 0;
        if ($lease->tds_applicable !== 'Yes') {
            $lease->tds_rate = 0;
        }
    }

    private function attachAgreement(Request $request, Lease $lease): void
    {
        if ($request->hasFile('agreement_document')) {
            $this->documents->store($request->file('agreement_document'), [
                'plot_id' => $lease->plot_id, 'documentable_type' => Lease::class, 'documentable_id' => $lease->id,
                'doc_type' => 'Registered lease deed', 'title' => 'Lease deed '.($lease->agreement_no ?: $lease->lease_no), 'is_original_scan' => 'Yes',
            ]);
        }
    }

    private function formData(Lease $lease): array
    {
        $plots = Plot::visibleTo()->with(['currentOwnership.shares.party'])
            ->when(! $lease->exists, fn ($q) => $q->whereDoesntHave('leases', fn ($l) => $l->where('status', 'Active'))->where('land_status', '!=', 'Dropped'))
            ->when($lease->exists, fn ($q) => $q->whereKey($lease->plot_id))
            ->orderBy('land_ref_no')->get();

        return [
            'lease' => $lease,
            'plotOpts' => $plots->mapWithKeys(fn ($p) => [$p->id => "$p->land_ref_no · survey ".$p->surveyLabel().' · '.fmt_num($p->area_under_lease_acres, 4).' ac'])->all(),
            'plot' => $lease->plot_id ? Plot::with(['park', 'spv', 'currentOwnership.shares.party'])->find($lease->plot_id) : null,
            'subUsedToday' => $lease->exists ? $this->capacity->subLeasedOn($lease) : 0,
            'agreements' => $lease->exists ? $lease->documents()->latest()->get() : collect(),
        ];
    }
}
