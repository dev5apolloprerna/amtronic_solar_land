<?php

namespace App\Http\Requests;

use App\Models\Park;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SpvRequest extends BaseRequest
{
    protected function prepareForValidation(): void
    {
        $this->upper(['spv_code']);
    }

    public function rules(): array
    {
        $s = $this->route('spv');

        return [
            'state_id' => ['required', 'exists:states,id'],
            'park_id' => ['required', 'exists:parks,id'],
            'spv_code' => $s ? ['prohibited'] : ['required', 'alpha_num', 'between:2,12', Rule::unique('spvs', 'spv_code')],
            'spv_name' => ['required', 'string', 'max:150'],
            'technology' => ['nullable', $this->opt('technology')],
            'capacity_mwp' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', $this->opt('spv_status')],
        ];
    }

    public function withValidator(Validator $v): void
    {
        $v->after(function (Validator $v) {
            if ($this->park_id && Park::whereKey($this->park_id)->value('state_id') != $this->state_id) {
                $v->errors()->add('park_id', 'Selected park is not in the selected state.');
            }
        });
    }

    public function messages(): array
    {
        return ['spv_code.prohibited' => 'SPV code is locked after save because land reference numbers use it.', 'spv_code.unique' => 'This SPV code already exists.'];
    }
}
