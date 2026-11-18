<?php

namespace App\Enums;

enum LandUnit: string
{
    case Acre = 'Acre';
    case Hectare = 'Hectare';
    case SqM = 'Sq.m';

    /** Convert an area in this unit to acres (4 decimals). */
    public function toAcres(float $area): float
    {
        return round(match ($this) {
            self::Acre => $area,
            self::Hectare => $area * 2.4710538,
            self::SqM => $area / 4046.8564,
        }, 4);
    }

    public static function acres(string $unit, float $area): float
    {
        return (self::tryFrom($unit) ?? self::Acre)->toAcres($area);
    }
}
