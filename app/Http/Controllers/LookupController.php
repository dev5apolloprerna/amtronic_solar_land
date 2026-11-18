<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Lease;
use App\Models\Park;
use App\Models\Plot;
use App\Models\RentSchedule;
use App\Models\Spv;
use App\Models\State;
use App\Models\Taluka;
use App\Services\CapacityService;
use App\Services\LandRefService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/** Small JSON endpoints for cascading dropdowns and live calculations in the drawer forms. */
class LookupController extends Controller
{
    public function cities(Request $request)
    {
        return City::where('state_id', $request->integer('state_id'))->where('status', 'Active')->orderBy('name')->get()
            ->map(fn ($c) => ['id' => $c->id, 'label' => "$c->name ($c->code)"]);
    }

    public function talukas(Request $request)
    {
        return Taluka::where('city_id', $request->integer('city_id'))->where('status', 'Active')->orderBy('name')->get()
            ->map(fn ($t) => ['id' => $t->id, 'label' => "$t->name ($t->code)"]);
    }

    /** SPVs of a park plus the park's location, used to pre-fill a new survey number. */
    public function spvs(Request $request)
    {
        $park = Park::find($request->integer('park_id'));

        return [
            'spvs' => Spv::visibleTo()->where('park_id', $park?->id)->where('status', 'Active')->orderBy('spv_code')->get()->map(fn ($s) => ['id' => $s->id, 'label' => "$s->spv_code · $s->spv_name"]),
            'park' => $park ? ['state_id' => $park->state_id, 'city_id' => $park->city_id, 'taluka_id' => $park->taluka_id] : null,
            'cities' => $park ? $this->cities(new Request(['state_id' => $park->state_id])) : [],
            'talukas' => $park ? $this->talukas(new Request(['city_id' => $park->city_id])) : [],
        ];
    }

    public function landRefPreview(Request $request, LandRefService $refs)
    {
        $park = Park::find($request->integer('park_id'));
        $state = State::find($request->integer('state_id'));
        $city = City::find($request->integer('city_id'));
        $taluka = Taluka::find($request->integer('taluka_id'));
        if (! $park || ! $state || ! $city || ! $taluka) {
            return ['ref' => null];
        }

        return ['ref' => $refs->preview($park, $state, $city, $taluka)];
    }

    /** Lease form defaults from a survey number + park capacity. */
    public function plot(Plot $plot, CapacityService $cap)
    {
        abort_unless(auth()->user()->canAccessSpv($plot->spv_id), 403);
        $plot->load(['park', 'spv', 'grid', 'currentOwnership.shares.party']);

        return [
            'land_ref_no' => $plot->land_ref_no,
            'area_under_lease_acres' => (float) $plot->area_under_lease_acres,
            'rate_per_acre' => (float) $plot->rate_per_acre,
            'increment_pct' => (float) $plot->increment_pct,
            'increment_after_yrs' => (int) $plot->increment_after_yrs,
            'connectivity_mw' => $plot->grid?->connectivity_mw ? (float) $plot->grid->connectivity_mw : null,
            'owners' => $plot->currentOwnership?->shares->map(fn ($s) => $s->party->party_name.' ('.fmt_num($s->share_percent, 4).'%)')->implode(', '),
            'park_code' => $plot->park->park_code,
            'spv_code' => $plot->spv->spv_code,
            'park_id' => $plot->park_id,
            'park_installed' => (float) $plot->park->installed_capacity_mw,
            'park_leased_today' => $cap->leasedTodayInPark($plot->park),
        ];
    }

    /** Free MW on a lease over a period (sub-lease drawer capacity bar). */
    public function leaseCapacity(Request $request, Lease $lease, CapacityService $cap)
    {
        abort_unless(auth()->user()->canAccessSpv($lease->spv_id), 403);
        $from = $request->date('start') ? Carbon::parse($request->date('start')) : $lease->lease_start_date->copy();
        $to = $request->date('end') ? Carbon::parse($request->date('end')) : $lease->lease_end_date->copy();
        if ($to->lt($from)) {
            $to = $from->copy();
        }
        $exclude = $request->integer('exclude') ?: null;
        $peak = $cap->peakSubLeased($lease, $from, $to, $exclude);

        return [
            'lease_no' => $lease->lease_no,
            'lease_mw' => (float) $lease->leased_capacity_mw,
            'lease_start' => $lease->lease_start_date->format('Y-m-d'),
            'lease_end' => $lease->lease_end_date->format('Y-m-d'),
            'from' => $from->format('d M Y'),
            'to' => $to->format('d M Y'),
            'peak' => $peak,
            'free' => round((float) $lease->leased_capacity_mw - $peak, 3),
            'sub_today' => $cap->subLeasedOn($lease),
        ];
    }

    public function parkCapacity(Request $request, Park $park, CapacityService $cap)
    {
        $from = $request->date('start') ?? now();
        $to = $request->date('end') ?? $from;
        $peak = $cap->peakLeased($park, Carbon::parse($from), Carbon::parse($to), $request->integer('exclude') ?: null);

        return ['installed' => (float) $park->installed_capacity_mw, 'peak' => $peak, 'free' => round((float) $park->installed_capacity_mw - $peak, 3)];
    }

    /** Landowner split of an instalment for the payment drawer. */
    public function allocations(RentSchedule $schedule)
    {
        abort_unless(auth()->user()->canAccessSpv($schedule->lease->spv_id), 403);

        return $schedule->allocations()->with('party')->get()->sortByDesc('share_percent')->values()->map(fn ($a) => [
            'id' => $a->id,
            'label' => $a->party->party_name.' ('.fmt_num($a->share_percent, 4).'%) · balance '.inr($a->remaining(), 2),
            'party' => $a->party->party_name,
            'share' => (float) $a->share_percent,
            'gross' => (float) $a->gross_amount,
            'tds' => (float) $a->tds_amount,
            'paid' => (float) $a->paid_amount,
            'remaining' => $a->remaining(),
            'tds_ratio' => (float) $a->gross_amount > 0 ? (float) $a->tds_amount / (float) $a->gross_amount : 0,
            'bank' => $a->party->bank_account_no ? $a->party->maskedBankAccount().' · '.$a->party->bank_ifsc.' · '.$a->party->bank_name_branch : null,
        ]);
    }
}
