<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LandAccess;
use App\Http\Requests\LeaseRequest;
use App\Models\Land;
use App\Models\Lease;
use App\Services\CapacityService;
use App\Services\RentScheduleService;
use Illuminate\Support\Facades\DB;

/** LEASE_MASTER · lease of a parcel; saving generates the rent schedule */
class LeaseController extends Controller
{
    use LandAccess;

    public function __construct(private RentScheduleService $schedules, private CapacityService $cap) {}

    public function create(Land $land)
    {
        $this->guardLand($land);
        $land->load('farmers');
        $first = $land->farmers->sortByDesc('ownership_pct')->first();
        $L = new Lease([
            'land_id' => $land->id,
            'lessor_name' => $land->farmers->pluck('owner_name')->implode(' & '),
            'lessor_pan' => $first?->owner_pan,
            'leased_area_acres' => $land->area_under_lease_acres,
            'leased_capacity_mw' => 0,
            'lease_status' => $land->activeLease()->exists() ? 'Draft' : 'Active',
            'lessor_state_id' => $land->state_id, 'lessor_district_id' => $land->district_id, 'lessor_city_id' => $land->city_id,
            'lease_duration_mths' => 360,
            'rent_rate_per_acre' => $land->rate_per_acre,
            'rent_frequency' => 'Annual',
            'escalation_pct' => $land->increment_pct,
            'escalation_every_yrs' => $land->increment_after_yrs,
            'tds_applicable' => 'Yes', 'tds_rate' => 10, 'gst_applicable' => 'No',
        ]);

        return $this->drawer('Add lease', 'LEASE_MASTER · '.$land->land_ref_no, 'lands.forms.lease', $this->formData($L, $land), module: 'lease');
    }

    public function store(LeaseRequest $request, Land $land)
    {
        $this->guardLand($land);
        [$from, $to] = $request->term();
        $L = DB::transaction(function () use ($request, $land, $to) {
            $d = $request->validated();
            $L = new Lease($d);
            $L->land_id = $land->id;
            $L->lease_end_date = $to;
            $L->rent_amount = RentScheduleService::instalment((float) $d['leased_area_acres'], (float) $d['rent_rate_per_acre'], $d['rent_frequency']);
            $L->save();
            $this->schedules->generate($L);
            $this->syncLandStatus($land);

            return $L;
        });

        return $this->ok("{$L->code} saved · rent schedule generated (".$L->schedules()->count().' instalments)', $this->tabUrl($land, 'lease'));
    }

    public function edit(Lease $lease)
    {
        $this->guardLand($lease->land);
        $ro = ! $this->canEdit('lease');

        return $this->drawer(($ro ? 'View' : 'Edit').' lease', 'LEASE_MASTER · '.$lease->code, 'lands.forms.lease', $this->formData($lease, $lease->land), $ro, 'lease');
    }

    public function update(LeaseRequest $request, Lease $lease)
    {
        $land = $lease->land;
        $this->guardLand($land);
        [$from, $to] = $request->term();
        $msg = DB::transaction(function () use ($request, $lease, $land, $to) {
            $d = $request->validated();
            $lease->fill($d);
            $lease->lease_end_date = $to;
            $lease->rent_amount = RentScheduleService::instalment((float) $d['leased_area_acres'], (float) $d['rent_rate_per_acre'], $d['rent_frequency']);
            if (! $lease->isDirty()) {
                return 'No changes to save';
            }
            $lease->save();
            $msg = 'Lease saved';
            if (RentScheduleService::termsChanged($lease)) {
                $n = $this->schedules->generate($lease);
                $msg .= " · rent schedule regenerated ($n unpaid instalments; paid ones kept)";
            }
            $this->syncLandStatus($land);

            return $msg;
        });

        return $this->ok($msg, $this->tabUrl($land, 'lease'));
    }

    public function destroy(Lease $lease)
    {
        $land = $lease->land;
        $this->guardLand($land);
        if ($lease->subleases()->exists()) {
            return $this->refuse("{$lease->code} has subleases. Delete the subleases first.");
        }
        if ($lease->schedules()->whereHas('payments')->exists()) {
            return $this->refuse("{$lease->code} has rent payments recorded. Set its status to Terminated instead of deleting it.");
        }
        DB::transaction(function () use ($lease, $land) {
            $lease->schedules()->delete();
            $lease->delete();
            $this->syncLandStatus($land);
        });

        return $this->ok("{$lease->code} deleted", $this->tabUrl($land, 'lease'));
    }

    private function formData(Lease $L, Land $land): array
    {
        $areaUsed = $L->exists ? $this->cap->areaUsed($L) : 0;
        $mwUsed = $L->exists ? $this->cap->mwPeak($L, $L->lease_start_date, $L->lease_end_date) : 0;
        $parkFree = null;
        if ($land->park && (float) $land->park->installed_capacity_mw > 0) {
            $parkFree = round((float) $land->park->installed_capacity_mw - $this->cap->parkLeasedToday($land->park) + ($L->exists && $L->lease_status === 'Active' ? (float) $L->leased_capacity_mw : 0), 3);
        }

        return ['L' => $L, 'land' => $land, 'areaUsed' => $areaUsed, 'mwUsed' => $mwUsed, 'parkFree' => $parkFree, 'hasPayments' => $L->exists && $L->schedules()->whereHas('payments')->exists()]
            + $this->locationOptions($L->lessor_state_id, $L->lessor_district_id);
    }

    /** Identified / ATL signed → Leased when an active lease exists; back to ATL signed if no active lease is left. */
    private function syncLandStatus(Land $land): void
    {
        $land->refresh();
        $active = $land->leases()->where('lease_status', 'Active')->exists();
        if ($active && in_array($land->land_status, ['Identified', 'ATL signed'], true)) {
            $land->update(['land_status' => 'Leased']);
        } elseif (! $active && in_array($land->land_status, ['Leased', 'Subleased'], true)) {
            $land->update(['land_status' => 'ATL signed']);
        }
    }
}
