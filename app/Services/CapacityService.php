<?php

namespace App\Services;

use App\Models\Lease;
use App\Models\Park;
use App\Models\SubLease;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * MW capacity with overlapping dates.
 * A new commitment is valid only if (peak MW already committed on any day of its period) + its MW <= the parent's MW.
 * The peak can only change on a start date, so we check the new period's start and every overlapping start inside it.
 */
class CapacityService
{
    public const EPS = 0.0005;

    /** Peak of the summed MW of $items on any day in [$from, $to]. Items need start/end dates and an MW value. */
    public static function peak(Collection $items, Carbon $from, Carbon $to, string $startKey, string $endKey, string $mwKey): float
    {
        $points = collect([$from->copy()->startOfDay()]);
        foreach ($items as $it) {
            $s = Carbon::parse($it->{$startKey})->startOfDay();
            if ($s->betweenIncluded($from, $to)) {
                $points->push($s);
            }
        }
        $max = 0.0;
        foreach ($points as $t) {
            $sum = $items->filter(fn ($it) => Carbon::parse($it->{$startKey})->lte($t) && Carbon::parse($it->{$endKey})->gte($t))->sum(fn ($it) => (float) $it->{$mwKey});
            $max = max($max, $sum);
        }

        return round($max, 3);
    }

    /* ---------- Sub-lease against its parent lease ---------- */

    public function overlappingSubLeases(Lease $lease, Carbon $from, Carbon $to, ?int $excludeId = null): Collection
    {
        return SubLease::where('lease_id', $lease->id)->where('status', 'Active')
            ->when($excludeId, fn ($q) => $q->whereKeyNot($excludeId))
            ->whereDate('start_date', '<=', $to)->whereDate('end_date', '>=', $from)->get();
    }

    public function peakSubLeased(Lease $lease, Carbon $from, Carbon $to, ?int $excludeId = null): float
    {
        return self::peak($this->overlappingSubLeases($lease, $from, $to, $excludeId), $from, $to, 'start_date', 'end_date', 'capacity_mw');
    }

    public function freeOnLease(Lease $lease, Carbon $from, Carbon $to, ?int $excludeId = null): float
    {
        return round((float) $lease->leased_capacity_mw - $this->peakSubLeased($lease, $from, $to, $excludeId), 3);
    }

    /** Throws a validation error when the sub-lease would over-commit its lease. */
    public function assertSubLeaseFits(Lease $lease, float $mw, Carbon $from, Carbon $to, ?int $excludeId = null): void
    {
        $peak = $this->peakSubLeased($lease, $from, $to, $excludeId);
        $free = round((float) $lease->leased_capacity_mw - $peak, 3);
        if ($mw > $free + self::EPS && $free <= self::EPS) {
            throw ValidationException::withMessages(['capacity_mw' => sprintf(
                'No MW of %s is free between %s and %s: all %s MW is already sub-leased at peak. Change the dates, or cancel or reduce another sub-lease first.',
                $lease->lease_no, $from->format('d M Y'), $to->format('d M Y'), self::fmt((float) $lease->leased_capacity_mw)
            )]);
        }
        if ($mw > $free + self::EPS) {
            throw ValidationException::withMessages(['capacity_mw' => sprintf(
                'Only %s MW of %s is free between %s and %s (peak %s MW already sub-leased of %s MW). Enter %s MW or less, or change the dates.',
                self::fmt(max(0, $free)), $lease->lease_no, $from->format('d M Y'), $to->format('d M Y'), self::fmt($peak),
                self::fmt((float) $lease->leased_capacity_mw), self::fmt(max(0, $free))
            )]);
        }
    }

    /** MW sub-leased (Active) on a given day, default today. */
    public function subLeasedOn(Lease $lease, ?Carbon $day = null): float
    {
        $day ??= now()->startOfDay();

        return round((float) SubLease::where('lease_id', $lease->id)->where('status', 'Active')
            ->whereDate('start_date', '<=', $day)->whereDate('end_date', '>=', $day)->sum('capacity_mw'), 3);
    }

    public function remainingToday(Lease $lease): float
    {
        return round((float) $lease->leased_capacity_mw - $this->subLeasedOn($lease), 3);
    }

    /** Lowest MW the lease may be reduced to: its peak sub-leased MW over its whole term. */
    public function minimumLeaseMw(Lease $lease, Carbon $from, Carbon $to): float
    {
        return $this->peakSubLeased($lease, $from, $to);
    }

    /* ---------- Lease against its park ---------- */

    public function overlappingLeases(Park $park, Carbon $from, Carbon $to, ?int $excludeId = null): Collection
    {
        return Lease::where('park_id', $park->id)->where('status', 'Active')
            ->when($excludeId, fn ($q) => $q->whereKeyNot($excludeId))
            ->whereDate('lease_start_date', '<=', $to)->whereDate('lease_end_date', '>=', $from)->get();
    }

    public function peakLeased(Park $park, Carbon $from, Carbon $to, ?int $excludeId = null): float
    {
        return self::peak($this->overlappingLeases($park, $from, $to, $excludeId), $from, $to, 'lease_start_date', 'lease_end_date', 'leased_capacity_mw');
    }

    public function assertLeaseFits(Park $park, float $mw, Carbon $from, Carbon $to, ?int $excludeId = null): void
    {
        $peak = $this->peakLeased($park, $from, $to, $excludeId);
        $free = round((float) $park->installed_capacity_mw - $peak, 3);
        if ($mw > $free + self::EPS && $free <= self::EPS) {
            throw ValidationException::withMessages(['leased_capacity_mw' => sprintf(
                'No MW of park %s is free for this lease period: all %s MW installed is already leased at peak. Change the dates or increase the park’s installed capacity.',
                $park->park_code, self::fmt((float) $park->installed_capacity_mw)
            )]);
        }
        if ($mw > $free + self::EPS) {
            throw ValidationException::withMessages(['leased_capacity_mw' => sprintf(
                'Only %s MW of park %s (%s MW installed) is free for this lease period; %s MW is already leased at peak. Enter %s MW or less.',
                self::fmt(max(0, $free)), $park->park_code, self::fmt((float) $park->installed_capacity_mw), self::fmt($peak), self::fmt(max(0, $free))
            )]);
        }
    }

    /** Lowest installed MW a park may be reduced to: peak MW of its active leases across all time. */
    public function minimumParkMw(Park $park): float
    {
        $leases = Lease::where('park_id', $park->id)->where('status', 'Active')->get();
        if ($leases->isEmpty()) {
            return 0.0;
        }

        return self::peak($leases, Carbon::parse($leases->min('lease_start_date')), Carbon::parse($leases->max('lease_end_date')), 'lease_start_date', 'lease_end_date', 'leased_capacity_mw');
    }

    /** Park MW currently under active lease (today). */
    public function leasedTodayInPark(Park $park): float
    {
        $today = now()->toDateString();

        return round((float) Lease::where('park_id', $park->id)->where('status', 'Active')
            ->whereDate('lease_start_date', '<=', $today)->whereDate('lease_end_date', '>=', $today)->sum('leased_capacity_mw'), 3);
    }

    public static function fmt(float $mw): string
    {
        return rtrim(rtrim(number_format($mw, 3, '.', ''), '0'), '.');
    }
}
