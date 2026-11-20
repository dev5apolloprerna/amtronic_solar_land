<?php

namespace App\Http\Controllers;

use App\Http\Requests\LandRequest;
use App\Models\City;
use App\Models\District;
use App\Models\Land;
use App\Models\Park;
use App\Models\Spv;
use App\Models\State;
use App\Services\CapacityService;
use App\Services\LandRefService;
use App\Services\ShareService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

/** LAND_MASTER list and the land parcel workspace (tabs as in the template). */
class LandController extends Controller
{
    public function __construct(private LandRefService $refs, private CapacityService $cap, private ShareService $shares) {}

    public function index(Request $request)
    {
        $q = Land::visibleTo()->with(['park', 'spv', 'city', 'state', 'surveys', 'farmers'])->orderBy('id');
        $rows = $this->search($q->get(), $request->q);

        if ($e = $this->maybeExport('land', 'Land Master', ['Land ref no.', 'Park', 'SPV', 'City / taluka', 'State', 'Survey nos.', 'Villages', 'Farmers', 'Lease area (ac)', 'Rate / acre', 'Increment', 'Status'],
            $rows->map(fn ($l) => [$l->land_ref_no, $l->park?->park_code ?? 'NOP', $l->spv->spv_code, $l->city->city_name, $l->state->state_code, $l->surveys->pluck('survey_plot_no')->implode(', '),
                $l->surveys->pluck('village')->unique()->implode(', '), $l->farmers->pluck('owner_name')->implode(', '), (float) $l->area_under_lease_acres, (float) $l->rate_per_acre,
                '+'.fmt_num($l->increment_pct).'% every '.$l->increment_after_yrs.' yrs', $l->land_status]))) {
            return $e;
        }

        return view('lands.index', ['rows' => $rows, 'total' => Land::visibleTo()->count()]);
    }

    /** New land parcel page (only the Land Master tab until saved). */
    public function create()
    {
        $land = new Land(['ref_year' => now()->year, 'land_status' => 'Identified', 'title_report' => 'No', 'tfr_confirmation' => 'No', 'title_dd_status' => 'Pending',
            'lessor_capacity_verified' => 'No', 'encumbrance_status' => 'Under verification', 'na_application' => 'No', 'na_status' => 'Not applied',
            'increment_after_yrs' => 3, 'increment_pct' => 5]);
        $this->shareNav(null, 'land');

        return view('lands.show', ['land' => null, 'form' => $land, 'tab' => 'land', 'editing' => true] + $this->formOptions($land));
    }

    public function store(LandRequest $request)
    {
        $d = $request->validated();
        $land = DB::transaction(function () use ($d) {
            $land = new Land($d);
            $land->parcel_code = $d['parcel_code'];
            $land->ref_year = $d['ref_year'];
            $land->area_acres_712 = round((float) $d['area_sqm_712'] / config('solar.sqm_per_acre'), 2);
            $land->land_ref_no = $this->refs->build(Spv::find($d['spv_id']), State::find($d['state_id']), isset($d['park_id']) ? Park::find($d['park_id']) : null, $d['parcel_code'], $d['ref_year']);
            $land->save();

            return $land;
        });

        return $this->ok("{$land->land_ref_no} saved · Next, add its survey / plot numbers and farmers in the new tabs", route('lands.show', $land));
    }

    public function show(Request $request, Land $land)
    {
        $this->authorizeLand($land);
        $tabs = $this->tabs();
        $tab = array_key_exists($request->query('tab'), $tabs) ? $request->query('tab') : 'land';
        $land->load(['park', 'spv', 'state', 'district', 'city', 'surveys', 'farmers', 'activeLease', 'compliance']);
        $editing = $tab === 'land' && $request->boolean('edit') && can_do('land', 'edit');
        $this->shareNav($land, $tab);

        $data = ['land' => $land, 'form' => $land, 'tab' => $tab, 'editing' => $editing, 'header' => $this->header($land)];
        if ($tab === 'land') {
            $data += $this->formOptions($land);
        } else {
            $data += $this->tabData($request, $land, $tab);
        }

        return view('lands.show', $data);
    }

    public function update(LandRequest $request, Land $land)
    {
        $this->authorizeLand($land);
        $d = collect($request->validated())->except(['parcel_code', 'ref_year', 'land_ref_no'])->all();
        $land->fill($d);
        $land->area_acres_712 = round((float) $d['area_sqm_712'] / config('solar.sqm_per_acre'), 2);
        if (! $land->isDirty()) {
            return $this->ok('No changes to save', route('lands.show', $land));
        }
        $land->save();

        return $this->ok('Land saved', route('lands.show', $land));
    }

    public function destroy(Land $land)
    {
        $this->authorizeLand($land);
        $k = [];
        foreach (['surveys' => 'survey numbers', 'farmers' => 'farmers', 'leases' => 'leases', 'documents' => 'documents'] as $rel => $label) {
            if ($land->{$rel}()->exists()) {
                $k[] = $label;
            }
        }
        if ($land->compliance()->exists()) {
            $k[] = 'a compliance record';
        }
        if ($k) {
            return $this->refuse("{$land->land_ref_no} is referenced by ".implode(', ', $k).'. The database blocks deletes that would orphan child rows.');
        }
        $land->delete();

        return $this->ok("{$land->land_ref_no} deleted", route('lands.index'));
    }

    /** Grid Connectivity · One-to-One (stored on the parcel). */
    public function grid(Request $request, Land $land)
    {
        $this->authorizeLand($land);
        $d = $request->validate([
            'connectivity_mw' => ['nullable', 'numeric', 'min:0'],
            'substation' => ['nullable', 'string', 'max:120'],
            'aggregator' => ['nullable', 'string', 'max:120'],
            'tfr_confirmation' => ['required', 'in:Yes,No'],
        ]);
        $land->update($d);

        return $this->ok('Grid connectivity saved', route('lands.show', [$land, 'tab' => 'grid']));
    }

    /** Excel / PDF of the list in a parcel tab. */
    public function export(Request $request, Land $land, string $tab)
    {
        $this->authorizeLand($land);
        abort_unless(array_key_exists($tab, $this->tabs()) && ! in_array($tab, ['land', 'grid'], true), 404);
        $d = $this->tabData($request, $land, $tab);
        [$headings, $rows] = TabExport::rows($tab, $d['rows']);

        return $this->maybeExport(config("solar.land_tabs.$tab.1"), $land->land_ref_no.' · '.config("solar.land_tabs.$tab.0"), $headings, $rows) ?? abort(400);
    }

    /* ---------------- helpers ---------------- */

    private function tabs(): array
    {
        return collect(config('solar.land_tabs'))->filter(fn ($t) => can_do($t[1]))->all();
    }

    /** Sub-menu under "Land Master" in the left nav, like the template. */
    private function shareNav(?Land $land, string $tab): void
    {
        $counts = [];
        if ($land) {
            $counts = [
                'survey' => $land->surveys()->count(), 'farmer' => $land->farmers()->count(), 'grid' => $land->substation ? 1 : 0,
                'lease' => $land->leases()->count(), 'schedule' => $land->schedules()->count(), 'payment' => $land->payments()->count(),
                'sublease' => $land->subleases()->count(), 'compliance' => $land->compliance()->count(), 'doc' => $land->documents()->count(),
            ];
        }
        View::share('landNav', ['land' => $land, 'tab' => $tab, 'tabs' => $land ? $this->tabs() : ['land' => config('solar.land_tabs.land')], 'counts' => $counts]);
    }

    private function header(Land $land): array
    {
        $L = $land->activeLease;
        $next = $land->schedules()->where('payment_status', '!=', 'Paid')->orderBy('due_date')->with('payments')->first();

        return [
            'lease' => $L,
            'next' => $next,
            'areaUsed' => $L ? $this->cap->areaUsed($L) : 0,
            'mwToday' => $L ? $this->cap->mwToday($L) : 0,
        ];
    }

    private function search(Collection $rows, ?string $q): Collection
    {
        if (! $q) {
            return $rows->values();
        }
        $q = mb_strtolower($q);

        return $rows->filter(fn ($r) => str_contains(mb_strtolower(json_encode($r->toArray(), JSON_UNESCAPED_UNICODE)), $q))->values();
    }

    private function tabData(Request $request, Land $land, string $tab): array
    {
        $rows = match ($tab) {
            'survey' => $land->surveys()->with(['city', 'district', 'state'])->get(),
            'farmer' => $land->farmers()->get(),
            'lease' => $land->leases()->with(['lessorCity', 'lessorDistrict'])->orderBy('id')->get(),
            'schedule' => $land->schedules()->with(['payments', 'lease'])->orderBy('lease_id')->orderBy('due_date')->get()
                ->when($request->lease, fn ($c, $v) => $c->where('lease_id', (int) $v))
                ->when($request->status, fn ($c, $v) => $c->filter(fn ($s) => $s->status() === $v)),
            'payment' => $land->payments()->with(['farmer', 'schedule'])->orderBy('id')->get(),
            'sublease' => $land->subleases()->with('lease')->orderBy('id')->get(),
            'compliance' => $land->compliance()->get(),
            'doc' => $land->documents()->with('uploader')->orderBy('id')->get(),
            default => collect(),
        };
        $total = $rows->count();
        $data = ['rows' => $this->search($rows, $request->q), 'total' => $total];

        if ($tab === 'survey') {
            $data['sqmTotal'] = (float) $land->surveys->sum('area_sqm');
        }
        if ($tab === 'farmer') {
            $data['allocations'] = $land->leases()->orderBy('id')->get()->map(function ($L) use ($land) {
                $s = $L->schedules()->first();
                $rent = (float) ($s->rent_amount ?? $L->rent_amount);
                $tds = (float) ($s->tds_amount ?? 0);

                return ['lease' => $L, 'rent' => $rent, 'tds' => $tds, 'net' => $rent - $tds, 'due' => $s->due_date ?? $L->lease_start_date,
                    'split' => $this->shares->split($land->farmers, $rent, $tds), 'valid' => $this->shares->ownershipOk($land)];
            });
        }
        if ($tab === 'payment') {
            $data['progress'] = $land->schedules()->with('payments')->orderBy('lease_id')->orderBy('due_date')->get()
                ->map(fn ($s) => ['s' => $s, 'shares' => $this->shares->forSchedule($s->setRelation('land', $land))]);
        }
        if ($tab === 'schedule') {
            $data['leaseOpts'] = $land->leases()->orderBy('id')->get()->mapWithKeys(fn ($l) => [$l->id => $l->code.' · '.$land->land_ref_no.' · '.fmt_num($l->leased_area_acres).' ac'])->all();
        }
        if ($tab === 'sublease' || $tab === 'lease') {
            $data['cap'] = $this->cap;
        }

        return $data;
    }

    private function formOptions(Land $land): array
    {
        $spvs = Spv::visibleTo()->when($land->park_id, fn ($q, $v) => $q->where('park_id', $v))->orderBy('id')->get();

        return [
            'parkOpts' => Park::orderBy('id')->get()->mapWithKeys(fn ($p) => [$p->id => "$p->park_code · $p->park_name"])->all(),
            'stateOpts' => State::stateOptions(),
            'districtOpts' => $land->state_id ? District::where('state_id', $land->state_id)->orderBy('id')->pluck('district_name', 'id')->all() : [],
            'cityOpts' => $land->district_id ? City::where('district_id', $land->district_id)->orderBy('id')->get()->mapWithKeys(fn ($c) => [$c->id => "$c->city_name · $c->city_type"])->all() : [],
            'spvOpts' => $spvs->mapWithKeys(fn ($s) => [$s->id => "$s->spv_code · $s->spv_name"])->all(),
        ];
    }

    private function authorizeLand(Land $land): void
    {
        abort_unless(auth()->user()->canAccessSpv($land->spv_id), 403, 'This land parcel belongs to an SPV you do not have access to.');
    }
}
