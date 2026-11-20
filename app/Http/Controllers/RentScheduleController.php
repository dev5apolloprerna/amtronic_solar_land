<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LandAccess;
use App\Http\Requests\ScheduleRequest;
use App\Models\Lease;
use App\Models\RentSchedule;
use App\Services\RentScheduleService;
use App\Services\ShareService;

/** RENT_SCHEDULE · generated from the lease; only due date, amount, hold and alert flag can be edited */
class RentScheduleController extends Controller
{
    use LandAccess;

    public function __construct(private ShareService $shares) {}

    public function edit(RentSchedule $schedule)
    {
        $this->guardLand($schedule->land);
        $ro = ! $this->canEdit('schedule');

        return $this->drawer(($ro ? 'View' : 'Edit').' rent instalment', 'RENT_SCHEDULE · '.$schedule->code.' · '.$schedule->lease->code, 'lands.forms.schedule',
            ['s' => $schedule->load('payments'), 'shares' => $this->shares->forSchedule($schedule)], $ro, 'schedule');
    }

    public function update(ScheduleRequest $request, RentSchedule $schedule)
    {
        $this->guardLand($schedule->land);
        $d = $request->validated();
        if ($schedule->payments()->exists() && abs((float) $d['rent_amount'] - (float) $schedule->rent_amount) > 0.001 && (float) $d['rent_amount'] < $schedule->totalPaid()) {
            return $this->refuse('Rent cannot be less than the '.inr($schedule->totalPaid()).' already paid against this instalment.');
        }
        $L = $schedule->lease;
        $d['tds_amount'] = $L->tds_applicable === 'Yes' ? round((float) $d['rent_amount'] * (float) $L->tds_rate / 100) : 0;
        $d['net_payable'] = (float) $d['rent_amount'] - $d['tds_amount'];
        $d['due_month'] = \Carbon\Carbon::parse($d['due_date'])->format('M Y');
        if ($d['payment_status'] === 'Partial Payment' && ! $schedule->payments()->exists()) {
            $d['payment_status'] = 'Pending';
        }
        $schedule->update($d);
        $schedule->refreshStatus();

        return $this->ok('Instalment saved', $this->tabUrl($schedule->land, 'schedule'));
    }

    /** Payment History modal: farmer-wise share, paid and remaining + every payment. */
    public function history(RentSchedule $schedule)
    {
        $this->guardLand($schedule->land);
        $schedule->load(['payments.farmer', 'lease', 'land']);

        return response()->json([
            'title' => 'Payment history · '.$schedule->code,
            'html' => view('lands.partials.payment-history', ['s' => $schedule, 'shares' => $this->shares->forSchedule($schedule)])->render(),
        ]);
    }

    /** Regenerate unpaid instalments of a lease from its current terms. */
    public function regenerate(Lease $lease, RentScheduleService $svc)
    {
        $this->guardLand($lease->land);
        $n = $svc->generate($lease);

        return $this->ok("Rent schedule of {$lease->code} regenerated · $n unpaid instalments", $this->tabUrl($lease->land, 'schedule'));
    }
}
