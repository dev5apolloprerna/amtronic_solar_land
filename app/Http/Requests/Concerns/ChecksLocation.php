<?php

namespace App\Http\Requests\Concerns;

use App\Models\City;
use App\Models\Taluka;
use Illuminate\Validation\Validator;

/** Ensures the chosen city belongs to the state and the taluka to the city. */
trait ChecksLocation
{
    protected function checkLocation(Validator $v, string $prefix = ''): void
    {
        $v->after(function (Validator $v) use ($prefix) {
            $state = $this->input($prefix.'state_id');
            $city = $this->input($prefix.'city_id');
            $taluka = $this->input($prefix.'taluka_id');
            if ($city && $state && City::whereKey($city)->value('state_id') != $state) {
                $v->errors()->add($prefix.'city_id', 'The selected city is not in the selected state.');
            }
            if ($taluka && $city && Taluka::whereKey($taluka)->value('city_id') != $city) {
                $v->errors()->add($prefix.'taluka_id', 'The selected taluka is not in the selected city.');
            }
        });
    }
}
