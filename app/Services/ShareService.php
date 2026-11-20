<?php

namespace App\Services;

use App\Models\Land;
use App\Models\RentSchedule;
use Illuminate\Support\Collection;

/**
 * Farmer-wise split of an instalment by ownership %, as in the template:
 * each farmer gets round(rent × % / 100); the last farmer gets the remainder so the split adds up exactly.
 */
class ShareService
{
    /** @return Collection<int, array{farmer: \App\Models\LandOwner, pct: float, share: float, tds: float, net: float, paid: float, remaining: float}> */
    public function forSchedule(RentSchedule $s): Collection
    {
        $farmers = $s->land->farmers;
        $payments = $s->payments()->get()->groupBy('farmer_id');

        return $this->split($farmers, (float) $s->rent_amount, (float) $s->tds_amount)->map(function ($row) use ($payments) {
            $row['paid'] = round((float) ($payments[$row['farmer']->id] ?? collect())->sum('gross_amount'), 2);
            $row['remaining'] = max(0, round($row['share'] - $row['paid'], 2));

            return $row;
        });
    }

    public function split(Collection $farmers, float $rent, float $tds): Collection
    {
        $carryG = 0;
        $carryT = 0;
        $last = $farmers->count() - 1;

        return $farmers->values()->map(function ($f, $i) use (&$carryG, &$carryT, $last, $rent, $tds) {
            $pct = (float) $f->ownership_pct;
            $g = $i === $last ? $rent - $carryG : round($rent * $pct / 100);
            $t = $i === $last ? $tds - $carryT : round($tds * $pct / 100);
            $carryG += $g;
            $carryT += $t;

            return ['farmer' => $f, 'pct' => $pct, 'share' => $g, 'tds' => $t, 'net' => $g - $t, 'paid' => 0.0, 'remaining' => $g];
        });
    }

    public function ownershipOk(Land $land): bool
    {
        return $land->farmers->isNotEmpty() && abs($land->ownershipTotal() - 100) < 0.0001;
    }
}
