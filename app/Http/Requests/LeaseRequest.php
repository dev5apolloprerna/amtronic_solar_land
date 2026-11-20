<?php

namespace App\Http\Requests;

use App\Models\Land;
use App\Models\Lease;
use App\Services\CapacityService;
use App\Services\RentScheduleService;
use Carbon\Carbon;
use Illuminate\Validation\Validator;

class LeaseRequest extends BaseRequest
{
    protected array $zeroIfBlank = ['security_deposit', 'escalation_pct', 'escalation_every_yrs'];

    protected function prepareForValidation(): void
    {
        $this->upper(['lessor_pan']);
        if ($this->tds_applicable !== 'Yes') {
            $this->merge(['tds_rate' => 0]);
        } elseif (! (float) $this->tds_rate) {
            $this->merge(['tds_rate' => 10]);
        }
    }

    public function rules(): array
    {
        return [
            'land_id' => $this->route('lease') ? ['prohibited'] : ['required', 'exists:lands,id'],
            'lessor_name' => ['required', 'string', 'max:200'],
            'lessor_pan' => ['nullable', 'regex:/^[A-Z]{5}\d{4}[A-Z]$/'],
            'leased_area_acres' => ['required', 'numeric', 'gt:0'],
            'leased_capacity_mw' => ['required', 'numeric', 'min:0'],
            'lease_status' => ['required', $this->opt('lease_status')],
            'lessor_address' => ['nullable', 'string', 'max:255'],
            'lessor_state_id' => ['required', 'exists:states,id'],
            'lessor_district_id' => ['required', 'exists:districts,id'],
            'lessor_city_id' => ['required', 'exists:cities,id'],
            'lessor_pincode' => ['nullable', 'digits:6'],
            'atl_date' => ['nullable', 'date'],
            'lease_date' => ['nullable', 'date'],
            'lease_start_date' => ['required', 'date'],
            'lease_duration_mths' => ['required', 'integer', 'min:1', 'max:1200'],
            'uni_number' => ['nullable', 'string', 'max:40'],
            'sro_doc_no' => ['nullable', 'string', 'max:40'],
            'agreement_no' => ['nullable', 'string', 'max:40'],
            'mutation_entry_no' => ['nullable', 'string', 'max:40'],
            'rent_rate_per_acre' => ['required', 'numeric', 'min:0'],
            'rent_frequency' => ['required', $this->opt('rent_frequency')],
            'security_deposit' => ['nullable', 'numeric', 'min:0'],
            'escalation_pct' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'escalation_every_yrs' => ['nullable', 'integer', 'min:0', 'max:99'],
            'tds_applicable' => ['required', $this->opt('yes_no')],
            'tds_rate' => ['nullable', 'numeric', 'min:0', 'max:30'],
            'gst_applicable' => ['required', $this->opt('yes_no')],
        ];
    }

    public function withValidator(Validator $v): void
    {
        $this->checkLocation($v, 'lessor_');
        $v->after(function (Validator $v) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }
            $lease = $this->route('lease');
            $land = $lease?->land ?? Land::find($this->land_id);
            if (! $this->user()->canAccessSpv($land->spv_id)) {
                $v->errors()->add('land_id', 'You do not have access to this parcel’s SPV.');

                return;
            }
            if ((float) $this->leased_area_acres > (float) $land->area_under_lease_acres + 0.001) {
                $v->errors()->add('leased_area_acres', 'Leased area ('.fmt_num($this->leased_area_acres).' ac) is more than the land’s area under lease ('.fmt_num($land->area_under_lease_acres).' ac).');
            }
            if ($this->lease_status === 'Active' && Lease::where('land_id', $land->id)->where('lease_status', 'Active')->when($lease, fn ($q) => $q->whereKeyNot($lease->id))->exists()) {
                $v->errors()->add('lease_status', $land->land_ref_no.' already has an active lease.');
            }
            $cap = app(CapacityService::class);
            [$from, $to] = $this->term();
            if ($lease) {
                $used = $cap->areaUsed($lease);
                if ((float) $this->leased_area_acres + 0.001 < $used) {
                    $v->errors()->add('leased_area_acres', fmt_num($used).' acres of this lease are already subleased. Leased area cannot go below that.');
                }
                $mw = $cap->mwPeak($lease, $from, $to);
                if ((float) $this->leased_capacity_mw + CapacityService::EPS < $mw) {
                    $v->errors()->add('leased_capacity_mw', CapacityService::fmt($mw).' MW of this lease is already subleased at peak. Leased capacity cannot go below that.');
                }
            }
            if ($this->lease_status === 'Active' && $land->park && (float) $land->park->installed_capacity_mw > 0) {
                $free = round((float) $land->park->installed_capacity_mw - $cap->parkPeak($land->park, $from, $to, $lease?->id), 3);
                if ((float) $this->leased_capacity_mw > $free + CapacityService::EPS) {
                    $v->errors()->add('leased_capacity_mw', 'Only '.CapacityService::fmt(max(0, $free)).' MW of park '.$land->park->park_code.' ('.CapacityService::fmt((float) $land->park->installed_capacity_mw).' MW installed) is free for this lease period.');
                }
            }
        });
    }

    /** @return array{0: Carbon, 1: Carbon} */
    public function term(): array
    {
        $start = Carbon::parse($this->lease_start_date)->startOfDay();

        return [$start, RentScheduleService::endDate($start->toDateString(), (int) $this->lease_duration_mths)];
    }

    public function messages(): array
    {
        return ['lessor_pan.regex' => 'PAN must look like ABCDE1234F.', 'lessor_pincode.digits' => 'PIN code must be 6 digits.'];
    }
}
