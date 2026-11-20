<?php

namespace App\Services;

use App\Models\Lease;
use App\Models\Park;
use App\Models\Sublease;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Sub-lease limits:
 *  - Area (template rule): non-terminated subleases of a lease can never exceed its leased acres.
 *  - Capacity (spec rule): active subleases running on the same day can never exceed the lease's MW;
 *    active leases in a park running on the same day can never exceed the park's installed MW.
 */
class CapacityService
{
    public const EPS = 0.0005;

    public function areaUsed(Lease $lease, ?int $exclude = null): float
    {
        return round((float) Sublease::where('lease_id', $lease->id)->where('status', '!=', 'Terminated')
            ->when($exclude, fn ($q) => $q->whereKeyNot($exclude))->sum('sublease_area_acres'), 2);
    }

    public function areaFree(Lease $lease, ?int $exclude = null): float
    {
        return round((float) $lease->leased_area_acres - $this->areaUsed($lease, $exclude), 2);
    }

    /** Highest summed value of $items running on any day in [$from, $to]. */
    public static function peak(Collection $items, Carbon $from, Carbon $to, string $s, string $e, string $v): float
    {
        $points = collect([$from->copy()->startOfDay()]);
        foreach ($items as $it) {
            $d = Carbon::parse($it->{$s})->startOfDay();
            if ($d->betweenIncluded($from, $to)) {
                $points->push($d);
            }
        }
        $max = 0.0;
        foreach ($points as $t) {
            $max = max($max, $items->filter(fn ($it) => Carbon::parse($it->{$s})->lte($t) && Carbon::parse($it->{$e})->gte($t))->sum(fn ($it) => (float) $it->{$v}));
        }

        return round($max, 3);
    }

    public function mwPeak(Lease $lease, Carbon $from, Carbon $to, ?int $exclude = null): float
    {
        $subs = Sublease::where('lease_id', $lease->id)->where('status', 'Active')->when($exclude, fn ($q) => $q->whereKeyNot($exclude))
            ->whereDate('sublease_start', '<=', $to)->whereDate('sublease_end', '>=', $from)->get();

        return self::peak($subs, $from, $to, 'sublease_start', 'sublease_end', 'capacity_mw');
    }

    public function mwFree(Lease $lease, Carbon $from, Carbon $to, ?int $exclude = null): float
    {
        return round((float) $lease->leased_capacity_mw - $this->mwPeak($lease, $from, $to, $exclude), 3);
    }

    public function mwToday(Lease $lease): float
    {
        $d = now()->toDateString();

        return round((float) Sublease::where('lease_id', $lease->id)->where('status', 'Active')->whereDate('sublease_start', '<=', $d)->whereDate('sublease_end', '>=', $d)->sum('capacity_mw'), 3);
    }

    /** MW of active leases in a park at their peak overlap with [$from, $to]. */
    public function parkPeak(Park $park, Carbon $from, Carbon $to, ?int $excludeLease = null): float
    {
        $leases = Lease::where('lease_status', 'Active')->whereHas('land', fn ($q) => $q->where('park_id', $park->id))
            ->when($excludeLease, fn ($q) => $q->whereKeyNot($excludeLease))
            ->whereDate('lease_start_date', '<=', $to)->whereDate('lease_end_date', '>=', $from)->get();

        return self::peak($leases, $from, $to, 'lease_start_date', 'lease_end_date', 'leased_capacity_mw');
    }

    public function parkLeasedToday(Park $park): float
    {
        $d = now()->toDateString();

        return round((float) Lease::where('lease_status', 'Active')->whereHas('land', fn ($q) => $q->where('park_id', $park->id))
            ->whereDate('lease_start_date', '<=', $d)->whereDate('lease_end_date', '>=', $d)->sum('leased_capacity_mw'), 3);
    }

    public static function fmt(float $v): string
    {
        return rtrim(rtrim(number_format($v, 3, '.', ''), '0'), '.');
    }
}
