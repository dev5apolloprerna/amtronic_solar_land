<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class DistrictRequest extends BaseRequest
{
    public function rules(): array
    {
        $d = $this->route('district');

        return [
            'state_id' => ['required', 'exists:states,id'],
            'district_name' => ['required', 'string', 'max:80', Rule::unique('districts', 'district_name')->where('state_id', $this->state_id)->ignore($d?->id)],
        ];
    }

    public function messages(): array
    {
        return ['district_name.unique' => 'This district already exists in the selected state.'];
    }
}
