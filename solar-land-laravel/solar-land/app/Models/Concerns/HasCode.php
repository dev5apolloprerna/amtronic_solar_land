<?php

namespace App\Models\Concerns;

/** Display code like the template's IDs: LND001, LSE001, RS0001 … built from the primary key. */
trait HasCode
{
    public function getCodeAttribute(): string
    {
        return static::CODE_PREFIX.str_pad((string) $this->getKey(), static::CODE_PAD ?? 3, '0', STR_PAD_LEFT);
    }
}
