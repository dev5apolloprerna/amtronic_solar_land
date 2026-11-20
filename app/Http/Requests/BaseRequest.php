<?php

namespace App\Http\Requests;

use App\Models\City;
use App\Models\District;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/** Shared helpers: role permission is checked by route middleware, so every request authorises. */
abstract class BaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** On nested create routes (/lands/{land}/...), the parcel always comes from the URL, never from the form. */
    /** Optional numeric fields whose column is NOT NULL with a default: a blank input is saved as 0. */
    protected array $zeroIfBlank = [];

    public function validateResolved(): void
    {
        if (($land = $this->route('land')) instanceof \App\Models\Land) {
            $this->merge(['land_id' => $land->id]);
        }
        foreach ($this->zeroIfBlank as $k) {
            if ($this->input($k) === null || $this->input($k) === '') {
                $this->merge([$k => 0]);
            }
        }
        parent::validateResolved();
    }

    protected function opt(string $key)
    {
        return Rule::in(config("solar.options.$key"));
    }

    /** District must be in the state; city / taluka must be in the district. */
    protected function checkLocation(Validator $v, string $p = ''): void
    {
        $v->after(function (Validator $v) use ($p) {
            $s = $this->input($p.'state_id');
            $d = $this->input($p.'district_id');
            $c = $this->input($p.'city_id');
            if ($d && $s && District::whereKey($d)->value('state_id') != $s) {
                $v->errors()->add($p.'district_id', 'The selected district is not in the selected state.');
            }
            if ($c && $d && City::whereKey($c)->value('district_id') != $d) {
                $v->errors()->add($p.'city_id', 'The selected city / taluka is not in the selected district.');
            }
        });
    }

    protected function upper(array $keys): void
    {
        $m = [];
        foreach ($keys as $k) {
            if ($this->has($k) && $this->input($k) !== null) {
                $m[$k] = strtoupper(trim((string) $this->input($k)));
            }
        }
        $this->merge($m);
    }
}
