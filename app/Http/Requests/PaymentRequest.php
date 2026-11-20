<?php

namespace App\Http\Requests;

use App\Models\RentSchedule;
use App\Services\ShareService;
use Illuminate\Validation\Validator;

class PaymentRequest extends BaseRequest
{
    protected array $zeroIfBlank = ['tds_deducted'];

    public function rules(): array
    {
        $p = $this->route('payment');

        return [
            'schedule_id' => $p ? ['prohibited'] : ['required', 'exists:rent_schedules,id'],
            'farmer_id' => ['required', 'exists:land_owners,id'],
            'payment_date' => ['required', 'date'],
            'gross_amount' => ['required', 'numeric', 'gt:0'],
            'tds_deducted' => ['nullable', 'numeric', 'min:0'],
            'tds_challan_no' => ['nullable', 'string', 'max:40'],
            'utr_reference' => ['required', 'string', 'max:60'],
            'sap_voucher_no' => ['nullable', 'string', 'max:40'],
        ];
    }

    public function withValidator(Validator $v): void
    {
        $v->after(function (Validator $v) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }
            $p = $this->route('payment');
            $s = $p?->schedule ?? RentSchedule::with('land.farmers')->find($this->schedule_id);
            if (! $this->user()->canAccessSpv($s->land->spv_id)) {
                $v->errors()->add('schedule_id', 'You do not have access to this parcel.');

                return;
            }
            $shares = app(ShareService::class);
            if (! $shares->ownershipOk($s->land)) {
                $v->errors()->add('farmer_id', 'Farmer ownership must total 100% before recording payments.');

                return;
            }
            $x = $shares->forSchedule($s)->first(fn ($r) => $r['farmer']->id == $this->farmer_id);
            if (! $x) {
                $v->errors()->add('farmer_id', 'Choose a valid farmer.');

                return;
            }
            $paidOthers = $x['paid'] - ($p && $p->farmer_id == $this->farmer_id ? (float) $p->gross_amount : 0);
            $left = round($x['share'] - $paidOthers, 2);
            if ((float) $this->gross_amount > $left + 0.01) {
                $v->errors()->add('gross_amount', 'Payment exceeds '.$x['farmer']->owner_name.' remaining balance of '.inr(max(0, $left)).'.');
            }
            if ((float) $this->tds_deducted > (float) $this->gross_amount) {
                $v->errors()->add('tds_deducted', 'TDS cannot exceed gross payment.');
            }
            if ((float) $this->tds_deducted > 0 && blank($this->tds_challan_no)) {
                $v->errors()->add('tds_challan_no', 'Enter TDS challan number.');
            }
        });
    }

    public function messages(): array
    {
        return ['gross_amount.gt' => 'Payment amount must be greater than zero.', 'farmer_id.required' => 'Choose a valid farmer.'];
    }
}
