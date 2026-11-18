<?php

namespace App\Http\Controllers;

use App\Enums\LandUnit;
use App\Http\Requests\PlotRequest;
use App\Models\Document;
use App\Models\Park;
use App\Models\Plot;
use App\Models\PlotOwnershipVersion;
use App\Models\RentPayment;
use App\Models\RentSchedule;
use App\Models\Spv;
use App\Models\SubLease;
use App\Services\CapacityService;
use App\Services\LandRefService;
use App\Support\LocationOptions;
use Illuminate\Http\Request;

/** Plot / survey number master and the per-plot workspace (tabs as in the HTML template). */
class PlotController extends Controller
{
    public const TABS = [
        'details' => ['Survey details', 'plot'],
        'owners' => ['Landowners & shares', 'ownership'],
        'grid' => ['Grid connectivity', 'grid'],
        'leases' => ['Leases', 'lease'],
        'schedule' => ['Rent schedule', 'schedule'],
        'payments' => ['Payments', 'payment'],
        'subleases' => ['Sub-leases', 'sublease'],
        'compliance' => ['Compliance', 'compliance'],
        'documents' => ['Documents', 'document'],
    ];

    public function __construct(private LandRefService $landRef, private CapacityService $capacity) {}

    public function index(Request $request)
    {
        $q = Plot::visibleTo()->with(['park', 'spv', 'city', 'taluka', 'state', 'currentOwnership.shares.party', 'activeLease'])
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('land_ref_no', 'like', "%$s%")->orWhere('survey_no', 'like', "%$s%")->orWhere('village', 'like', "%$s%")))
            ->when($request->park_id, fn ($q, $v) => $q->where('park_id', $v))
            ->when($request->spv_id, fn ($q, $v) => $q->where('spv_id', $v))
            ->when($request->land_status, fn ($q, $v) => $q->where('land_status', $v))
            ->orderBy('land_ref_no');

        if ($export = $this->maybeExport('plot', 'Survey Number Register', ['Land ref no.', 'Park', 'SPV', 'State', 'City', 'Taluka', 'Village', 'Survey no.', 'Sub survey', 'Area', 'Unit', 'Area (ac)',
            'Under lease (ac)', 'Rate / acre', 'Owners', 'Ownership total %', 'Status'],
            $q->get()->map(fn ($p) => [$p->land_ref_no, $p->park->park_code, $p->spv->spv_code, $p->state->name, $p->city->name, $p->taluka->name, $p->village, $p->survey_no, $p->sub_survey_no,
                (float) $p->total_area, $p->land_unit, (float) $p->area_acres, (float) $p->area_under_lease_acres, (float) $p->rate_per_acre,
                $p->currentOwnership?->shares->map(fn ($s) => $s->party->party_name.' '.fmt_num($s->share_percent, 4).'%')->implode('; '),
                $p->currentOwnership ? (float) $p->currentOwnership->total_percent : 0, $p->land_status]))) {
            return $export;
        }

        return view('plots.index', [
            'rows' => $q->paginate(config('solar.per_page'))->withQueryString(),
            'total' => Plot::visibleTo()->count(),
            'parks' => Park::orderBy('park_code')->pluck('park_code', 'id'),
            'spvs' => Spv::visibleTo()->orderBy('spv_code')->pluck('spv_code', 'id'),
        ]);
    }

    public function create()
    {
        $plot = new Plot(['land_unit' => 'Acre', 'increment_after_yrs' => 3, 'increment_pct' => 5, 'title_report' => 'No', 'title_dd_status' => 'Pending',
            'lessor_capacity_verified' => 'No', 'encumbrance_status' => 'Under verification', 'na_application' => 'No', 'na_status' => 'Not applied', 'land_status' => 'Identified']);

        return view('plots.create', ['plot' => $plot] + $this->formOptions($plot));
    }

    public function store(PlotRequest $request)
    {
        $plot = $this->landRef->createPlot($request->payload());

        return $this->ok("{$plot->land_ref_no} saved · Next, add its landowners in the Landowners & shares tab", route('plots.show', [$plot, 'tab' => 'owners']));
    }

    public function show(Request $request, Plot $plot)
    {
        $this->authorizePlot($plot);
        $user = $request->user();
        $tabs = array_filter(self::TABS, fn ($t) => $user->hasPermission($t[1], 'view'));
        $tab = array_key_exists($request->query('tab'), $tabs) ? $request->query('tab') : 'details';
        $plot->load(['park', 'spv', 'state', 'city', 'taluka', 'grid', 'compliance', 'currentOwnership.shares.party', 'draftOwnership', 'activeLease']);

        $lease = $plot->activeLease;
        $next = RentSchedule::where('plot_id', $plot->id)->whereIn('status', ['Pending', 'Partially paid'])->orderBy('due_date')->first();
        $data = compact('plot', 'tabs', 'tab', 'lease', 'next') + [
            'subToday' => $lease ? $this->capacity->subLeasedOn($lease) : 0,
            'editing' => $tab === 'details' && $request->boolean('edit') && $user->hasPermission('plot', 'edit'),
            'counts' => [
                'owners' => $plot->currentOwnership?->shares->count() ?? 0,
                'grid' => $plot->grid ? 1 : 0,
                'leases' => $plot->leases()->count(),
                'schedule' => $plot->rentSchedules()->count(),
                'payments' => RentPayment::whereHas('allocation.schedule', fn ($q) => $q->where('plot_id', $plot->id))->count(),
                'subleases' => SubLease::whereHas('lease', fn ($q) => $q->where('plot_id', $plot->id))->count(),
                'compliance' => $plot->compliance ? 1 : 0,
                'documents' => $plot->documents()->count(),
            ],
        ];

        $data += match ($tab) {
            'details' => $data['editing'] ? $this->formOptions($plot) : [],
            'owners' => $this->ownersTab($request, $plot),
            'leases' => ['leases' => $plot->leases()->with(['park', 'spv'])->withCount('subLeases')->latest('lease_start_date')->get()],
            'schedule' => ['schedules' => RentSchedule::where('plot_id', $plot->id)->with(['lease', 'allocations'])->orderBy('due_date')->paginate(50)->withQueryString()],
            'payments' => ['payments' => RentPayment::whereHas('allocation.schedule', fn ($q) => $q->where('plot_id', $plot->id))
                ->with(['allocation.party', 'allocation.schedule.lease'])->latest('payment_date')->paginate(50)->withQueryString()],
            'subleases' => ['subleases' => SubLease::whereHas('lease', fn ($q) => $q->where('plot_id', $plot->id))->with(['lease', 'party'])->latest('start_date')->get(),
                'plotLeases' => $plot->leases()->where('status', 'Active')->get()],
            'documents' => ['documents' => Document::where('plot_id', $plot->id)->with('uploader')->latest()->get()],
            default => [],
        };

        return view('plots.show', $data);
    }

    public function update(PlotRequest $request, Plot $plot)
    {
        $this->authorizePlot($plot);
        $data = $request->payload();
        $plot->fill($data);
        $plot->area_acres = LandUnit::acres($data['land_unit'], (float) $data['total_area']);
        $plot->save();

        return $this->ok("{$plot->land_ref_no} saved", route('plots.show', $plot));
    }

    public function destroy(Plot $plot)
    {
        $this->authorizePlot($plot);
        $uses = [];
        foreach (['leases' => 'leases', 'documents' => 'documents', 'rentSchedules' => 'rent instalments'] as $rel => $label) {
            if ($plot->{$rel}()->exists()) {
                $uses[] = $label;
            }
        }
        if ($plot->compliance()->exists()) {
            $uses[] = 'a compliance record';
        }
        if (PlotOwnershipVersion::where('plot_id', $plot->id)->where('status', '!=', 'Draft')->exists()) {
            $uses[] = 'finalised ownership';
        }
        if ($uses) {
            return $this->refuse("{$plot->land_ref_no} is referenced by ".implode(', ', $uses).'. Set its status to Dropped instead.');
        }
        $plot->delete();

        return $this->ok("{$plot->land_ref_no} deleted", route('plots.index'));
    }

    private function ownersTab(Request $request, Plot $plot): array
    {
        $versions = $plot->ownershipVersions()->with(['shares.party', 'finaliser'])->get();
        $selected = $request->query('ver') ? $versions->firstWhere('id', (int) $request->query('ver')) : null;
        $selected ??= $versions->firstWhere('status', 'Draft') ?? $versions->first(fn ($v) => $v->status === 'Finalised' && ! $v->effective_to) ?? $versions->first();
        $lease = $plot->activeLease;
        $preview = $lease ? RentSchedule::where('lease_id', $lease->id)->whereIn('status', ['Pending', 'Partially paid'])->with('allocations.party')->orderBy('due_date')->first() : null;

        return compact('versions', 'selected', 'preview');
    }

    private function formOptions(Plot $plot): array
    {
        return [
            'parks' => Park::where('status', 'Active')->orWhere('id', $plot->park_id)->orderBy('park_code')->get()->mapWithKeys(fn ($p) => [$p->id => "$p->park_code · $p->park_name"]),
            'spvOpts' => $plot->park_id ? Spv::visibleTo()->where('park_id', $plot->park_id)->get()->mapWithKeys(fn ($s) => [$s->id => "$s->spv_code · $s->spv_name"])->all() : [],
        ] + LocationOptions::for($plot);
    }

    private function authorizePlot(Plot $plot): void
    {
        abort_unless(auth()->user()->canAccessSpv($plot->spv_id), 403, 'This survey number belongs to an SPV you do not have access to.');
    }
}
