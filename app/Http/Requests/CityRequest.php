<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge(['code' => strtoupper(trim((string) $this->code))]);
        }
    }

    public function rules(): array
    {
        $city = $this->route('city');
        $locked = $city?->codeLocked();

        return [
            'state_id' => $locked ? ['prohibited'] : ['required', 'exists:states,id'],
            'name' => ['required', 'string', 'max:80', Rule::unique('cities', 'name')->where('state_id', $locked ? $city->state_id : $this->state_id)->ignore($city?->id)],
            'code' => $locked ? ['prohibited'] : ['required', 'alpha_num', 'between:2,5', Rule::unique('cities', 'code')->where('state_id', $this->state_id)->ignore($city?->id)],
            'status' => ['required', Rule::in(config('solar.options.status'))],
        ];
    }

    public function messages(): array
    {
        return ['code.prohibited' => 'The city code is locked because Land Reference Numbers already use it.', 'state_id.prohibited' => 'The state is locked because Land Reference Numbers already use this city.',
            'name.unique' => 'This city already exists in the selected state.', 'code.unique' => 'This code is already used by another city in the state.'];
    }
}
