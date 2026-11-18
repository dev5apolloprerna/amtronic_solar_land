<?php

namespace App\Http\Requests;

use App\Enums\LandUnit;
use App\Models\Lease;
use App\Models\Plot;
use App\Models\SubLease;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class LeaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $lease = $this->route('lease');
        $o = fn ($k) => Rule::in(config("solar.options.$k"));

        return [
            'plot_id' => $lease ? ['prohibited'] : ['required', 'exists:plots,id'],
            'lease_date' => ['nullable', 'date'],
            'atl_date' => ['nullable', 'date'],
            'registration_date' => ['nullable', 'date'],
            'lease_start_date' => ['required', 'date'],
            'lease_duration_months' => ['required', 'integer', 'min:1', 'max:1200'],
            'total_lease_area' => ['required', 'numeric', 'gt:0'],
            'land_unit' => ['required', $o('land_unit')],
            'leased_capacity_mw' => ['required', 'numeric', 'min:0', 'max:999999'],
            'rent_type' => ['required', $o('rent_type')],
            'rent_rate' => ['required', 'numeric', 'min:0'],
            'payment_frequency' => ['required', $o('payment_frequency')],
            'escalation_pct' => ['required', 'numeric', 'min:0', 'max:100'],
            'escalation_every_yrs' => ['required', 'integer', 'min:0', 'max:99'],
            'security_deposit' => ['nullable', 'numeric', 'min:0'],
            'tds_applicable' => ['required', $o('yes_no')],
            'tds_rate' => ['required_if:tds_applicable,Yes', 'nullable', 'numeric', 'min:0', 'max:30'],
            'gst_applicable' => ['required', $o('yes_no')],
            'uni_number' => ['nullable', 'string', 'max:40'],
            'sro_doc_no' => ['nullable', 'string', 'max:40'],
            'agreement_no' => ['nullable', 'string', 'max:40'],
            'mutation_entry_no' => ['nullable', 'string', 'max:40'],
            'status' => ['required', $lease ? $o('lease_status') : Rule::in(['Draft', 'Active'])],
            'agreement_document' => ['nullable', 'file', 'max:'.config('solar.upload.max_kb'), 'mimes:'.implode(',', config('solar.upload.extensions')), 'extensions:'.implode(',', config('solar.upload.extensions'))],
        ];
    }

    public function withValidator(Validator $v): void
    {
        $v->after(function (Validator $v) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }
            $lease = $this->route('lease');
            $plot = $lease ? $lease->plot : Plot::find($this->plot_id);
            if (! $this->user()->canAccessSpv($plot->spv_id)) {
                $v->errors()->add('plot_id', 'You do not have access to this survey number’s SPV.');

                return;
            }
            $acres = LandUnit::acres($this->land_unit, (float) $this->total_lease_area);
            if ($acres > (float) $plot->area_under_lease_acres + 0.0001) {
                $v->errors()->add('total_lease_area', 'Lease area ('.fmt_num($acres, 4).' ac) is more than the survey number’s area under lease ('.fmt_num($plot->area_under_lease_acres, 4).' ac).');
            }
            if ($this->status === 'Active' && Lease::where('plot_id', $plot->id)->where('status', 'Active')->when($lease, fn ($q) => $q->whereKeyNot($lease->id))->exists()) {
                $v->errors()->add('status', $plot->land_ref_no.' already has an active lease.');
            }
            if ($lease) {
                [$start, $end] = $this->term();
                $outside = SubLease::where('lease_id', $lease->id)->whereIn('status', ['Draft', 'Active'])
                    ->where(fn ($q) => $q->whereDate('start_date', '<', $start)->orWhereDate('end_date', '>', $end))->pluck('sub_lease_no');
                if ($outside->isNotEmpty()) {
                    $v->errors()->add('lease_start_date', 'Sub-leases '.$outside->implode(', ').' fall outside the new lease term. Change them first.');
                }
                if (in_array($this->status, ['Terminated', 'Draft'], true) && SubLease::where('lease_id', $lease->id)->where('status', 'Active')->exists()) {
                    $v->errors()->add('status', 'This lease has active sub-leases. Cancel or expire them before changing the lease to '.$this->status.'.');
                }
            }
        });
    }

    /** [start, end] where end = start + months − 1 day. */
    public function term(): array
    {
        $start = Carbon::parse($this->lease_start_date)->startOfDay();

        return [$start, $start->copy()->addMonthsNoOverflow((int) $this->lease_duration_months)->subDay()];
    }
}
