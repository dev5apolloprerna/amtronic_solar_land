<?php

namespace App\Enums;

enum PaymentFrequency: string
{
    case Annual = 'Annual';
    case HalfYearly = 'Half-yearly';
    case Quarterly = 'Quarterly';
    case Monthly = 'Monthly';

    /** Months covered by one instalment. */
    public function months(): int
    {
        return match ($this) {
            self::Annual => 12,
            self::HalfYearly => 6,
            self::Quarterly => 3,
            self::Monthly => 1,
        };
    }

    public static function monthsFor(string $value): int
    {
        return (self::tryFrom($value) ?? self::Annual)->months();
    }
}
