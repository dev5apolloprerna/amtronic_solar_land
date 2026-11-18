<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SpvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(array_filter([
            'spv_code' => $this->has('spv_code') ? strtoupper(trim((string) $this->spv_code)) : null,
            'cin' => $this->cin ? strtoupper(trim((string) $this->cin)) : null,
        ], fn ($v) => $v !== null));
    }

    public function rules(): array
    {
        $spv = $this->route('spv');

        return [
            'spv_code' => $spv ? ['prohibited'] : ['required', 'alpha_num', 'between:2,12', Rule::unique('spvs', 'spv_code')],
            'spv_name' => ['required', 'string', 'max:150'],
            'park_id' => ['required', 'exists:parks,id'],
            'party_id' => ['nullable', Rule::exists('parties', 'id')->where('party_type', 'Company')],
            'cin' => ['nullable', 'regex:/^[LU]\d{5}[A-Z]{2}\d{4}[A-Z]{3}\d{6}$/'],
            'incorporation_date' => ['nullable', 'date', 'before_or_equal:today'],
            'registered_address' => ['nullable', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:120'],
            'mobile' => ['nullable', 'regex:/^[6-9]\d{9}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'technology' => ['required', Rule::in(config('solar.options.technology'))],
            'capacity_mwp' => ['nullable', 'numeric', 'min:0'],
            'project_status' => ['required', Rule::in(config('solar.options.project_status'))],
            'status' => ['required', Rule::in(config('solar.options.status'))],
        ];
    }

    public function messages(): array
    {
        return ['spv_code.prohibited' => 'SPV code is locked after saving.', 'cin.regex' => 'CIN must be 21 characters, e.g. U40106GJ2023PTC123456.',
            'party_id.exists' => 'Choose a party of type Company as the SPV owner.', 'mobile.regex' => 'Mobile must be a 10-digit Indian number.'];
    }
}
