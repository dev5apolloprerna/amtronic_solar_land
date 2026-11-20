<?php

namespace App\Http\Requests;

use App\Models\Park;
use App\Models\Spv;
use App\Models\State;
use App\Services\LandRefService;
use Illuminate\Validation\Validator;

class LandRequest extends BaseRequest
{
    protected function prepareForValidation(): void
    {
        $this->upper(['parcel_code']);
        if ($this->park_id === '') {
            $this->merge(['park_id' => null]);
        }
    }

    public function rules(): array
    {
        $l = $this->route('land');

        return [
            'park_id' => ['nullable', 'exists:parks,id'],
            'state_id' => ['required', 'exists:states,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'spv_id' => ['required', 'exists:spvs,id'],
            'parcel_code' => $l ? ['prohibited'] : ['required', 'alpha_num', 'max:10'],
            'ref_year' => $l ? ['prohibited'] : ['required', 'integer', 'between:2000,2100'],
            'area_sqm_712' => ['required', 'numeric', 'gt:0'],
            'area_under_lease_acres' => ['required', 'numeric', 'min:0'],
            'dlr_sqm' => ['nullable', 'numeric', 'min:0'],
            'rate_per_acre' => ['required', 'numeric', 'min:0'],
            'increment_after_yrs' => ['required', 'integer', 'min:0', 'max:99'],
            'increment_pct' => ['required', 'numeric', 'min:0', 'max:100'],
            'title_report' => ['nullable', $this->opt('yes_no')],
            'title_dd_status' => ['nullable', $this->opt('title_dd_status')],
            'lessor_capacity_verified' => ['nullable', $this->opt('yes_no')],
            'encumbrance_status' => ['nullable', $this->opt('encumbrance_status')],
            'lien_details' => ['nullable', 'string', 'max:2000'],
            'na_application' => ['nullable', $this->opt('yes_no')],
            'na_status' => ['nullable', $this->opt('na_status')],
            'land_status' => ['required', $this->opt('land_status')],
        ];
    }

    public function withValidator(Validator $v): void
    {
        $this->checkLocation($v);
        $v->after(function (Validator $v) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }
            $spv = Spv::find($this->spv_id);
            if ($this->park_id && $spv->park_id != $this->park_id) {
                $v->errors()->add('spv_id', 'Selected SPV does not belong to this park.');
            }
            if (! $this->user()->canAccessSpv($spv->id)) {
                $v->errors()->add('spv_id', 'You do not have access to this SPV.');
            }
            $acres = round((float) $this->area_sqm_712 / config('solar.sqm_per_acre'), 2);
            if ((float) $this->area_under_lease_acres > $acres + 0.001) {
                $v->errors()->add('area_under_lease_acres', 'Area under lease ('.fmt_num($this->area_under_lease_acres).' ac) cannot exceed the 7/12 area ('.fmt_num($acres).' ac).');
            }
            if (! $this->route('land')) {
                $ref = app(LandRefService::class)->build($spv, State::find($this->state_id), $this->park_id ? Park::find($this->park_id) : null, $this->parcel_code, $this->ref_year);
                if ($ref && app(LandRefService::class)->exists($ref)) {
                    $v->errors()->add('parcel_code', "$ref already exists. Use a different parcel code or year.");
                }
            }
        });
    }

    public function messages(): array
    {
        return ['parcel_code.prohibited' => 'Parcel code is locked after save because the land reference number uses it.',
            'ref_year.prohibited' => 'Reference year is locked after save because the land reference number uses it.'];
    }
}
