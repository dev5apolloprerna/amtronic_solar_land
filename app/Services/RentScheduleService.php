<?php

namespace App\Services;

use App\Models\Lease;
use App\Models\RentSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Rent schedule generator (same rules as the template):
 * annual rent = leased acres × rent per acre; instalment = annual × months/12 × (1 + escalation%)^floor(year / every);
 * instalments with any payment are kept; the rest are regenerated.
 */
class RentScheduleService
{
    public const FREQ = ['Annual' => 12, 'Half-yearly' => 6, 'Quarterly' => 3, 'Monthly' => 1];

    public static function months(string $freq): int
    {
        return self::FREQ[$freq] ?? 12;
    }

    public static function endDate(string $start, int $months): Carbon
    {
        return Carbon::parse($start)->addMonthsNoOverflow($months)->subDay();
    }

    /** Year-1 rent per instalment. */
    public static function instalment(float $acres, float $rate, string $freq): float
    {
        return round($acres * $rate * self::months($freq) / 12);
    }

    public function generate(Lease $lease): int
    {
        $made = AuditService::muted(fn () => DB::transaction(function () use ($lease) {
            $keep = RentSchedule::where('lease_id', $lease->id)->where(fn ($q) => $q->has('payments')->orWhere('payment_status', 'Paid'))->get();
            RentSchedule::where('lease_id', $lease->id)->whereNotIn('id', $keep->pluck('id'))->delete();
            $keptDates = $keep->map(fn ($s) => $s->due_date->toDateString())->all();

            $fm = self::months($lease->rent_frequency);
            $n = intdiv((int) $lease->lease_duration_mths, $fm);
            $annual = (float) $lease->leased_area_acres * (float) $lease->rent_rate_per_acre;
            $every = (int) $lease->escalation_every_yrs;
            $made = 0;
            for ($i = 0; $i < $n; $i++) {
                $due = $lease->lease_start_date->copy()->addMonthsNoOverflow($i * $fm);
                if (in_array($due->toDateString(), $keptDates, true)) {
                    continue;
                }
                $year = intdiv($i * $fm, 12);
                $steps = $every > 0 ? intdiv($year, $every) : 0;
                $amt = round($annual * $fm / 12 * pow(1 + (float) $lease->escalation_pct / 100, $steps));
                $tds = $lease->tds_applicable === 'Yes' ? round($amt * (float) $lease->tds_rate / 100) : 0;
                RentSchedule::create([
                    'lease_id' => $lease->id, 'land_id' => $lease->land_id, 'due_month' => $due->format('M Y'), 'due_date' => $due,
                    'rent_amount' => $amt, 'tds_amount' => $tds, 'net_payable' => $amt - $tds, 'payment_status' => 'Pending', 'alert_sent_flag' => 'No',
                ]);
                $made++;
            }

            return $made;
        }));

        return $made;
    }

    public static function termsChanged(Lease $lease): bool
    {
        return $lease->wasChanged(['leased_area_acres', 'rent_rate_per_acre', 'rent_frequency', 'escalation_pct', 'escalation_every_yrs', 'lease_start_date',
            'lease_duration_mths', 'tds_applicable', 'tds_rate']);
    }
}
