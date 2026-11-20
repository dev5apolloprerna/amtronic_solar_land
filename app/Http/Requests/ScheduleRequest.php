<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ScheduleRequest extends BaseRequest
{
    public function rules(): array
    {
        $s = $this->route('schedule');

        return [
            'due_date' => ['required', 'date'],
            'rent_amount' => ['required', 'numeric', 'min:0'],
            'payment_status' => ['required', Rule::in($s->payment_status === 'Paid' ? ['Paid'] : ['Pending', 'On hold', 'Partial Payment'])],
            'alert_sent_flag' => ['required', $this->opt('yes_no')],
        ];
    }
}
