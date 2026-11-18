<?php

namespace App\Http\Requests;

use App\Models\Plot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ComplianceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rec = $this->route('compliance');
        $o = fn ($k) => Rule::in(config("solar.options.$k"));

        return [
            'plot_id' => $rec ? ['prohibited'] : ['required', 'exists:plots,id', Rule::unique('compliance_records', 'plot_id')],
            'mortgage_created' => ['required', $o('yes_no')],
            'mortgage_date' => ['nullable', 'required_if:mortgage_created,Yes', 'date'],
            'lender_name' => ['nullable', 'required_if:mortgage_created,Yes', 'string', 'max:150'],
            'cersai_reg_no' => ['nullable', 'string', 'max:40'],
            'original_docs_custody' => ['required', $o('docs_custody')],
            'custodian_name' => ['nullable', 'string', 'max:150'],
            'docs_deposit_date' => ['nullable', 'date'],
            'lender_noc_status' => ['required', $o('lender_noc_status')],
            'pdc_open_items' => ['required', 'integer', 'min:0', 'max:999'],
            'target_closure_date' => ['nullable', 'date'],
            'escalation_level' => ['required', $o('escalation_level')],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator(Validator $v): void
    {
        $v->after(function (Validator $v) {
            $plot = $this->route('compliance')?->plot ?? Plot::find($this->plot_id);
            if ($plot && ! $this->user()->canAccessSpv($plot->spv_id)) {
                $v->errors()->add('plot_id', 'You do not have access to this survey number.');
            }
        });
    }

    public function messages(): array
    {
        return ['plot_id.unique' => 'This survey number already has a compliance record. Edit that one instead.',
            'lender_name.required_if' => 'Enter the lender name for a mortgaged survey number.', 'mortgage_date.required_if' => 'Enter the mortgage date.'];
    }
}
