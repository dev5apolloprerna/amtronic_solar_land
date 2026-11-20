<?php

namespace App\Http\Controllers\Concerns;

use App\Models\City;
use App\Models\District;
use App\Models\Land;
use App\Models\State;

trait LandAccess
{
    protected function guardLand(Land $land): void
    {
        abort_unless(auth()->user()->canAccessSpv($land->spv_id), 403, 'This land parcel belongs to an SPV you do not have access to.');
    }

    protected function tabUrl(Land $land, string $tab): string
    {
        return route('lands.show', [$land, 'tab' => $tab]);
    }

    /** State / district / city option lists for cascaded selects. */
    protected function locationOptions($stateId, $districtId): array
    {
        return [
            'stateOpts' => State::stateOptions(),
            'districtOpts' => $stateId ? District::where('state_id', $stateId)->orderBy('id')->pluck('district_name', 'id')->all() : [],
            'cityOpts' => $districtId ? City::where('district_id', $districtId)->orderBy('id')->get()->mapWithKeys(fn ($c) => [$c->id => "$c->city_name · $c->city_type"])->all() : [],
        ];
    }
}
