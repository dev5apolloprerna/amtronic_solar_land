<?php

namespace App\Services;

use App\Enums\PaymentFrequency;
use App\Models\Lease;
use App\Models\RentSchedule;
use Illuminate\Support\Facades\DB;

/**
 * Builds the rent schedule of a lease:
 * instalment = annual rent x months/12 x (1 + escalation%) ^ floor(lease year / escalate-every years)
 */
class RentScheduleService
{
    public function __construct(private RentAllocationService $allocations) {}

    public static function annualRent(string $rentType, float $rate, float $areaAcres): float
    {
        return round($rentType === 'Fixed annual' ? $rate : $rate * $areaAcres, 2);
    }

    /**
     * (Re)generate unpaid instalments. Paid / part-paid instalments are kept as they are.
     * Draft and Terminated leases get no new instalments. Returns the number generated.
     */
    public function generate(Lease $lease): int
    {
        $made = AuditService::muted(fn () => $this->build($lease));
        app(AuditService::class)->log('schedule', 'UPDATE', $lease->id, $lease->lease_no, 'rent_schedule', null, "$made unpaid instalments generated", force: true);

        return $made;
    }

    private function build(Lease $lease): int
    {
        return DB::transaction(function () use ($lease) {
            $kept = $lease->schedules()->whereIn('status', ['Paid', 'Partially paid'])->pluck('installment_no')->all();
            $lease->schedules()->whereNotIn('status', ['Paid', 'Partially paid'])->get()->each->delete();

            if (! in_array($lease->status, ['Active', 'Expired'], true)) {
                return 0;
            }

            $fm = PaymentFrequency::monthsFor($lease->payment_frequency);
            $count = intdiv((int) $lease->lease_duration_months, $fm);
            $annual = (float) $lease->annual_rent;
            $every = (int) $lease->escalation_every_yrs;
            $esc = (float) $lease->escalation_pct;
            $tdsRate = $lease->tds_applicable === 'Yes' ? (float) $lease->tds_rate : 0;
            $made = 0;

            for ($i = 0; $i < $count; $i++) {
                $no = $i + 1;
                if (in_array($no, $kept, true)) {
                    continue;
                }
                $due = $lease->lease_start_date->copy()->addMonthsNoOverflow($i * $fm);
                $year = intdiv($i * $fm, 12);
                $steps = $every > 0 ? intdiv($year, $every) : 0;
                $amount = round($annual * $fm / 12 * pow(1 + $esc / 100, $steps), 2);
                $tds = round($amount * $tdsRate / 100, 2);

                $schedule = RentSchedule::create([
                    'lease_id' => $lease->id,
                    'plot_id' => $lease->plot_id,
                    'installment_no' => $no,
                    'period_from' => $due,
                    'period_to' => $due->copy()->addMonthsNoOverflow($fm)->subDay(),
                    'due_date' => $due,
                    'rent_amount' => $amount,
                    'tds_amount' => $tds,
                    'net_payable' => round($amount - $tds, 2),
                    'status' => 'Pending',
                    'alert_sent' => 'No',
                ]);
                $this->allocations->allocate($schedule);
                $made++;
            }

            return $made;
        });
    }

    /** Whether a change to these fields needs the schedule rebuilt. */
    public static function termsChanged(Lease $lease): bool
    {
        return $lease->wasChanged(['lease_start_date', 'lease_duration_months', 'annual_rent', 'payment_frequency', 'escalation_pct',
            'escalation_every_yrs', 'tds_applicable', 'tds_rate', 'status', 'plot_id']);
    }
}
