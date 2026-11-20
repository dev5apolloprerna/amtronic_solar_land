<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LandAccess;
use App\Http\Requests\FarmerRequest;
use App\Models\Land;
use App\Models\LandOwner;

/** LAND_OWNER · farmers / owners of a parcel with ownership % */
class FarmerController extends Controller
{
    use LandAccess;

    public function create(Land $land)
    {
        $this->guardLand($land);
        $left = max(0, round(100 - $land->ownershipTotal(), 4));

        return $this->drawer('Add farmer / owner', 'LAND_OWNER · '.$land->land_ref_no, 'lands.forms.farmer',
            ['f' => new LandOwner(['land_id' => $land->id, 'ownership_pct' => $left ?: null]), 'land' => $land, 'left' => $left], module: 'farmer');
    }

    public function store(FarmerRequest $request, Land $land)
    {
        $this->guardLand($land);
        LandOwner::create(['land_id' => $land->id] + $request->payload());
        $total = $land->fresh('farmers')->ownershipTotal();

        return $this->ok('Farmer saved'.($total < 100 ? ' · ownership now '.fmt_num($total).'%, add farmers until it totals 100%' : ''), $this->tabUrl($land, 'farmer'));
    }

    public function edit(LandOwner $farmer)
    {
        $this->guardLand($farmer->land);
        $ro = ! $this->canEdit('farmer');
        $left = max(0, round(100 - $farmer->land->ownershipTotal() + (float) $farmer->ownership_pct, 4));

        return $this->drawer(($ro ? 'View' : 'Edit').' farmer / owner', 'LAND_OWNER · '.$farmer->code, 'lands.forms.farmer', ['f' => $farmer, 'land' => $farmer->land, 'left' => $left], $ro, 'farmer');
    }

    public function update(FarmerRequest $request, LandOwner $farmer)
    {
        $this->guardLand($farmer->land);
        $farmer->update($request->payload());

        return $this->ok('Farmer saved', $this->tabUrl($farmer->land, 'farmer'));
    }

    public function destroy(LandOwner $farmer)
    {
        $this->guardLand($farmer->land);
        if ($farmer->payments()->withTrashed()->exists()) {
            return $this->refuse("{$farmer->owner_name} has rent payments recorded. Farmers with payments cannot be deleted.");
        }
        $farmer->delete();

        return $this->ok("{$farmer->owner_name} removed", $this->tabUrl($farmer->land, 'farmer'));
    }
}
