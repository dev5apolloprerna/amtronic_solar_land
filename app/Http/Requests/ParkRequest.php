<?php

namespace App\Http\Requests;

use App\Services\CapacityService;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ParkRequest extends BaseRequest
{
    protected array $zeroIfBlank = ['installed_capacity_mw'];

    protected function prepareForValidation(): void
    {
        $this->upper(['park_code']);
    }

    public function rules(): array
    {
        $p = $this->route('park');

        return [
            'park_code' => $p ? ['prohibited'] : ['required', 'alpha_num', 'between:2,6', Rule::unique('parks', 'park_code')],
            'park_name' => ['required', 'string', 'max:150'],
            'state_id' => ['required', 'exists:states,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'total_area_acres' => ['nullable', 'numeric', 'min:0'],
            'installed_capacity_mw' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function withValidator(Validator $v): void
    {
        $this->checkLocation($v);
        $v->after(function (Validator $v) {
            $p = $this->route('park');
            if ($p && is_numeric($this->installed_capacity_mw)) {
                $used = app(CapacityService::class)->parkLeasedToday($p);
                if ((float) $this->installed_capacity_mw + CapacityService::EPS < $used) {
                    $v->errors()->add('installed_capacity_mw', 'Active leases already use '.CapacityService::fmt($used).' MW of this park. Installed capacity cannot go below that.');
                }
            }
        });
    }

    public function messages(): array
    {
        return ['park_code.prohibited' => 'Park code is locked after save because land reference numbers use it.', 'park_code.unique' => 'This park code already exists.'];
    }
}
