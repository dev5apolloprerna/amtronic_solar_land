<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // route middleware checks the role permission
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge(['code' => strtoupper(trim((string) $this->code))]);
        }
    }

    public function rules(): array
    {
        $state = $this->route('state');
        $locked = $state?->codeLocked();

        return [
            'name' => ['required', 'string', 'max:80', Rule::unique('states', 'name')->ignore($state?->id)],
            'code' => $locked ? ['prohibited'] : ['required', 'alpha', 'size:2', Rule::unique('states', 'code')->ignore($state?->id)],
            'status' => ['required', Rule::in(config('solar.options.status'))],
        ];
    }

    public function messages(): array
    {
        return ['code.prohibited' => 'The state code is locked because Land Reference Numbers already use it.', 'code.size' => 'State code must be exactly 2 letters, e.g. GJ.'];
    }
}
