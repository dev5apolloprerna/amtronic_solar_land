<?php

namespace App\Http\Requests;

use App\Models\Lease;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SubLeaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sub = $this->route('sublease');
        $o = fn ($k) => Rule::in(config("solar.options.$k"));

        return [
            'lease_id' => $sub ? ['prohibited'] : ['required', 'exists:leases,id'],
            'party_id' => ['required', Rule::exists('parties', 'id')->whereIn('party_type', ['Sub-lessee', 'Company', 'Other'])],
            'capacity_mw' => ['required', 'numeric', 'gt:0', 'max:999999'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'rent_amount' => ['nullable', 'numeric', 'min:0'],
            'rent_frequency' => ['required', $o('payment_frequency')],
            'payment_terms' => ['nullable', 'string', 'max:2000'],
            'agreement_no' => ['nullable', 'string', 'max:40'],
            'lender_noc_obtained' => ['required', $o('yes_no')],
            'status' => ['required', $sub ? $o('sublease_status') : Rule::in(['Draft', 'Active'])],
            'agreement_document' => ['nullable', 'file', 'max:'.config('solar.upload.max_kb'), 'mimes:'.implode(',', config('solar.upload.extensions')), 'extensions:'.implode(',', config('solar.upload.extensions'))],
        ];
    }

    public function withValidator(Validator $v): void
    {
        $v->after(function (Validator $v) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }
            $sub = $this->route('sublease');
            $lease = $sub ? $sub->lease : Lease::with('plot.compliance')->find($this->lease_id);
            if (! $this->user()->canAccessSpv($lease->spv_id)) {
                $v->errors()->add('lease_id', 'You do not have access to this lease’s SPV.');

                return;
            }
            if (! $sub && $lease->status !== 'Active') {
                $v->errors()->add('lease_id', $lease->lease_no.' is '.$lease->status.'. Sub-leases can only be carved out of an active lease.');
            }
            if ($this->start_date < $lease->lease_start_date->format('Y-m-d')) {
                $v->errors()->add('start_date', 'Sub-lease cannot start before the lease starts ('.fmt_date($lease->lease_start_date).').');
            }
            if ($this->end_date > $lease->lease_end_date->format('Y-m-d')) {
                $v->errors()->add('end_date', 'Sub-lease cannot end after the lease ends ('.fmt_date($lease->lease_end_date).').');
            }
            $cm = $lease->plot->compliance;
            if ($this->status === 'Active' && $cm && $cm->mortgage_created === 'Yes' && $this->lender_noc_obtained !== 'Yes') {
                $v->errors()->add('lender_noc_obtained', $lease->plot->land_ref_no.' is mortgaged to '.($cm->lender_name ?: 'a lender').'. An active sub-lease needs the lender’s NOC.');
            }
        });
    }

    public function messages(): array
    {
        return ['party_id.exists' => 'Choose a party of type Sub-lessee, Company or Other.', 'capacity_mw.gt' => 'Sub-lease capacity must be more than 0 MW.',
            'end_date.after_or_equal' => 'Sub-lease end must be on or after its start.'];
    }
}
