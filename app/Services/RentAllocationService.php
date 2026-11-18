<?php

namespace App\Services;

use App\Models\Plot;
use App\Models\RentSchedule;
use Carbon\Carbon;

/**
 * Splits each rent instalment across the landowners in force on its due date.
 * Rounding: each owner gets round(amount x share%), the last owner gets the remainder,
 * so the split always adds up to the instalment to the paisa.
 */
class RentAllocationService
{
    public function allocate(RentSchedule $schedule): void
    {
        // Never touch an instalment that already has payments: its split is history.
        if ($schedule->allocations()->where('paid_amount', '>', 0)->exists()) {
            return;
        }

        $version = app(OwnershipService::class)->versionOn($schedule->plot_id, $schedule->due_date);
        $schedule->allocations()->delete();
        $schedule->ownership_version_id = $version?->id;
        $schedule->save();

        if (! $version || $version->shares->isEmpty()) {
            return;
        }

        $shares = $version->shares->sortByDesc('share_percent')->values();
        $gross = (float) $schedule->rent_amount;
        $tds = (float) $schedule->tds_amount;
        $gSum = 0.0;
        $tSum = 0.0;
        $last = $shares->count() - 1;

        foreach ($shares as $i => $share) {
            $pct = (float) $share->share_percent;
            $g = $i === $last ? round($gross - $gSum, 2) : round($gross * $pct / 100, 2);
            $t = $i === $last ? round($tds - $tSum, 2) : round($tds * $pct / 100, 2);
            $gSum += $g;
            $tSum += $t;
            $schedule->allocations()->create([
                'party_id' => $share->party_id,
                'share_percent' => $pct,
                'gross_amount' => $g,
                'tds_amount' => $t,
                'net_amount' => round($g - $t, 2),
                'paid_amount' => 0,
            ]);
        }
    }

    /** Re-split every unpaid instalment of a plot due on or after a date (after an ownership change). */
    public function reallocateFrom(Plot $plot, Carbon $from): int
    {
        $n = AuditService::muted(function () use ($plot, $from) {
            $n = 0;
            RentSchedule::where('plot_id', $plot->id)->whereDate('due_date', '>=', $from)->whereIn('status', ['Pending', 'On hold'])
                ->each(function (RentSchedule $s) use (&$n) {
                    $this->allocate($s);
                    $n++;
                });

            return $n;
        });
        app(AuditService::class)->log('schedule', 'UPDATE', $plot->id, $plot->land_ref_no, 'rent_allocation', null, "$n unpaid instalments re-allocated from ".$from->format('Y-m-d'), force: true);

        return $n;
    }
}
