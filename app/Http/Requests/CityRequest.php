<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CityRequest extends BaseRequest
{
    public function rules(): array
    {
        $c = $this->route('city');

        return [
            'state_id' => ['required', 'exists:states,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'city_name' => ['required', 'string', 'max:80', Rule::unique('cities', 'city_name')->where('district_id', $this->district_id)->ignore($c?->id)],
            'city_type' => ['nullable', $this->opt('city_type')],
        ];
    }

    public function withValidator(Validator $v): void
    {
        $this->checkLocation($v);
    }

    public function messages(): array
    {
        return ['city_name.unique' => 'This city / taluka already exists in the selected district.'];
    }
}
