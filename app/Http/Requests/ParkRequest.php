<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ChecksLocation;
use App\Services\CapacityService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ParkRequest extends FormRequest
{
    use ChecksLocation;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('park_code')) {
            $this->merge(['park_code' => strtoupper(trim((string) $this->park_code))]);
        }
    }

    public function rules(): array
    {
        $park = $this->route('park');

        return [
            // Park code is locked after the first save: it is part of every Land Reference Number.
            'park_code' => $park ? ['prohibited'] : ['required', 'alpha_num', 'between:2,6', Rule::unique('parks', 'park_code')],
            'park_name' => ['required', 'string', 'max:150'],
            'state_id' => ['required', 'exists:states,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'taluka_id' => ['required', 'exists:talukas,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'total_land_area' => ['required', 'numeric', 'min:0'],
            'land_unit' => ['required', Rule::in(config('solar.options.land_unit'))],
            'installed_capacity_mw' => ['required', 'numeric', 'min:0', 'max:9999999'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(config('solar.options.status'))],
        ];
    }

    public function withValidator(Validator $v): void
    {
        $this->checkLocation($v);
        $v->after(function (Validator $v) {
            $park = $this->route('park');
            if ($park && is_numeric($this->installed_capacity_mw)) {
                $min = app(CapacityService::class)->minimumParkMw($park);
                if ((float) $this->installed_capacity_mw + CapacityService::EPS < $min) {
                    $v->errors()->add('installed_capacity_mw', 'Active leases already commit '.CapacityService::fmt($min).' MW at peak. Installed capacity cannot go below that.');
                }
            }
        });
    }

    public function messages(): array
    {
        return ['park_code.prohibited' => 'Park code is locked after saving because Land Reference Numbers use it.', 'park_code.unique' => 'This park code already exists.'];
    }
}
