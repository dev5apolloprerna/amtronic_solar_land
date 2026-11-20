<?php

namespace App\Services;

use App\Models\Land;
use App\Models\Park;
use App\Models\Spv;
use App\Models\State;

/**
 * Land reference number, as in the template: SPV-STATE-PARK-PARCEL-YEAR, e.g. PGP10-GJ-BHV-A1-2024.
 * "NOP" replaces the park code when the parcel has no park. Generated once and never changed.
 */
class LandRefService
{
    public function build(?Spv $spv, ?State $state, ?Park $park, ?string $parcel, $year): ?string
    {
        if (! $spv || ! $state || ! $parcel || ! $year) {
            return null;
        }

        return strtoupper($spv->spv_code).'-'.strtoupper($state->state_code).'-'.($park ? strtoupper($park->park_code) : 'NOP').'-'.strtoupper(trim($parcel)).'-'.(int) $year;
    }

    public function exists(string $ref): bool
    {
        return Land::withTrashed()->where('land_ref_no', $ref)->exists();
    }
}
