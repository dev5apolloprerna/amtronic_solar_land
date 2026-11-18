<?php

namespace App\Support;

use App\Models\City;
use App\Models\State;
use App\Models\Taluka;
use Illuminate\Database\Eloquent\Model;

/** State / city / taluka dropdown options for a record that has state_id, city_id, taluka_id. */
class LocationOptions
{
    public static function for(?Model $m, string $prefix = ''): array
    {
        $stateId = $m?->{$prefix.'state_id'};
        $cityId = $m?->{$prefix.'city_id'};

        return [
            'stateOpts' => State::where('status', 'Active')->orWhere('id', $stateId)->orderBy('name')->get()->mapWithKeys(fn ($s) => [$s->id => "$s->name ($s->code)"])->all(),
            'cityOpts' => $stateId ? City::where('state_id', $stateId)->orderBy('name')->get()->mapWithKeys(fn ($c) => [$c->id => "$c->name ($c->code)"])->all() : [],
            'talukaOpts' => $cityId ? Taluka::where('city_id', $cityId)->orderBy('name')->get()->mapWithKeys(fn ($t) => [$t->id => "$t->name ($t->code)"])->all() : [],
        ];
    }
}
