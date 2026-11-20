<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ComplianceRequest extends BaseRequest
{
    public function rules(): array
    {
        $c = $this->route('compliance');

        return [
            'land_id' => $c ? ['prohibited'] : ['required', 'exists:lands,id', Rule::unique('compliances', 'land_id')],
            'mortgage_created' => ['required', $this->opt('yes_no')],
            'mortgage_date' => ['nullable', 'date'],
            'lender_name' => ['nullable', 'required_if:mortgage_created,Yes', 'string', 'max:150'],
            'cersai_reg_no' => ['nullable', 'string', 'max:40'],
            'original_docs_custody' => ['required', $this->opt('docs_custody')],
            'custodian_name' => ['nullable', 'string', 'max:150'],
            'docs_deposit_date' => ['nullable', 'date'],
            'lender_noc_status' => ['required', $this->opt('lender_noc_status')],
            'pdc_open_items' => ['nullable', 'integer', 'min:0', 'max:999'],
            'target_closure_date' => ['nullable', 'date'],
            'escalation_level' => ['required', $this->opt('escalation_level')],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return ['land_id.unique' => 'This parcel already has a compliance record. Edit that one instead.', 'lender_name.required_if' => 'Enter the lender name for a mortgaged parcel.'];
    }

    public function payload(): array
    {
        $d = $this->validated();
        if ($d['mortgage_created'] !== 'Yes') {
            $d['mortgage_date'] = null;
        }
        $d['pdc_open_items'] = (int) ($d['pdc_open_items'] ?? 0);

        return $d;
    }
}
