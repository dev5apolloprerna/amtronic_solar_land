<?php

namespace App\Http\Requests;

use App\Models\Lease;
use App\Services\CapacityService;
use Carbon\Carbon;
use Illuminate\Validation\Validator;

class SubleaseRequest extends BaseRequest
{
    protected function prepareForValidation(): void
    {
        $this->upper(['sublessee_pan']);
    }

    public function rules(): array
    {
        return [
            'lease_id' => $this->route('sublease') ? ['prohibited'] : ['required', 'exists:leases,id'],
            'status' => ['required', $this->opt('sublease_status')],
            'sublessee_name' => ['required', 'string', 'max:150'],
            'sublessee_pan' => ['nullable', 'regex:/^[A-Z]{5}\d{4}[A-Z]$/'],
            'sublease_area_acres' => ['required', 'numeric', 'gt:0'],
            'capacity_mw' => ['required', 'numeric', 'min:0'],
            'sublease_rent' => ['nullable', 'numeric', 'min:0'],
            'sublease_start' => ['required', 'date'],
            'sublease_end' => ['required', 'date', 'after_or_equal:sublease_start'],
            'agreement_no' => ['nullable', 'string', 'max:40'],
            'lender_noc_obtained' => ['required', $this->opt('yes_no')],
        ];
    }

    public function withValidator(Validator $v): void
    {
        $v->after(function (Validator $v) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }
            $sub = $this->route('sublease');
            $L = $sub?->lease ?? Lease::with('land.compliance')->find($this->lease_id);
            if (! $this->user()->canAccessSpv($L->land->spv_id)) {
                $v->errors()->add('lease_id', 'You do not have access to this lease.');

                return;
            }
            $cap = app(CapacityService::class);
            if ($this->status !== 'Terminated') {
                $free = $cap->areaFree($L, $sub?->id);
                if ((float) $this->sublease_area_acres > $free + 0.001) {
                    $v->errors()->add('sublease_area_acres', 'Only '.fmt_num(max(0, $free)).' acres of '.$L->code.' are free to sublease. Reduce the area to '.fmt_num(max(0, $free)).' acres or less.');
                }
            }
            if ($this->status === 'Active' && (float) $this->capacity_mw > 0) {
                $from = Carbon::parse($this->sublease_start);
                $to = Carbon::parse($this->sublease_end);
                $free = $cap->mwFree($L, $from, $to, $sub?->id);
                if ((float) $this->capacity_mw > $free + CapacityService::EPS) {
                    $v->errors()->add('capacity_mw', $free <= CapacityService::EPS
                        ? 'No MW of '.$L->code.' ('.CapacityService::fmt((float) $L->leased_capacity_mw).' MW) is free between '.$from->format('d M Y').' and '.$to->format('d M Y').'.'
                        : 'Only '.CapacityService::fmt($free).' MW of '.$L->code.' is free between '.$from->format('d M Y').' and '.$to->format('d M Y').'. Enter '.CapacityService::fmt($free).' MW or less.');
                }
            }
            if ($this->sublease_start < $L->lease_start_date->toDateString()) {
                $v->errors()->add('sublease_start', 'Sublease cannot start before the lease starts ('.fmt_date($L->lease_start_date).').');
            }
            if ($this->sublease_end > $L->lease_end_date->toDateString()) {
                $v->errors()->add('sublease_end', 'Sublease cannot end after the lease ends ('.fmt_date($L->lease_end_date).').');
            }
            $cm = $L->land->compliance;
            if ($this->status === 'Active' && $cm && $cm->mortgage_created === 'Yes' && $this->lender_noc_obtained !== 'Yes') {
                $v->errors()->add('lender_noc_obtained', $L->land->land_ref_no.' is mortgaged to '.$cm->lender_name.'. An active sublease needs the lender’s NOC.');
            }
        });
    }

    public function messages(): array
    {
        return ['sublessee_pan.regex' => 'PAN must look like ABCDE1234F.', 'sublease_end.after_or_equal' => 'Sublease end must be after its start.',
            'sublease_area_acres.gt' => 'Sublease area must be more than 0.'];
    }
}
