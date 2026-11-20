<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StateRequest extends BaseRequest
{
    protected function prepareForValidation(): void
    {
        $this->upper(['state_code']);
    }

    public function rules(): array
    {
        $st = $this->route('state');

        return [
            'state_name' => ['required', 'string', 'max:80', Rule::unique('states', 'state_name')->ignore($st?->id)],
            // locked after save because land reference numbers use it
            'state_code' => $st ? ['prohibited'] : ['required', 'alpha', 'size:2', Rule::unique('states', 'state_code')],
        ];
    }

    public function messages(): array
    {
        return ['state_code.prohibited' => 'State code is locked after save because land reference numbers use it.', 'state_code.size' => 'State code must be 2 letters, e.g. GJ.',
            'state_name.unique' => 'A state with this name or code already exists.', 'state_code.unique' => 'A state with this name or code already exists.'];
    }
}
