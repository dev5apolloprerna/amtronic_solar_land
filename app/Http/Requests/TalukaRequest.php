<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TalukaRequest extends FormRequest
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
        $taluka = $this->route('taluka');
        $locked = $taluka?->codeLocked();
        $cityId = $locked ? $taluka->city_id : $this->city_id;

        return [
            'city_id' => $locked ? ['prohibited'] : ['required', 'exists:cities,id'],
            'name' => ['required', 'string', 'max:80', Rule::unique('talukas', 'name')->where('city_id', $cityId)->ignore($taluka?->id)],
            'code' => $locked ? ['prohibited'] : ['required', 'alpha_num', 'between:2,5', Rule::unique('talukas', 'code')->where('city_id', $cityId)->ignore($taluka?->id)],
            'status' => ['required', Rule::in(config('solar.options.status'))],
        ];
    }

    public function messages(): array
    {
        return ['code.prohibited' => 'The taluka code is locked because Land Reference Numbers already use it.', 'city_id.prohibited' => 'The city is locked because Land Reference Numbers already use this taluka.',
            'name.unique' => 'This taluka already exists in the selected city.', 'code.unique' => 'This code is already used by another taluka in the city.'];
    }
}
