<?php

namespace App\Services;

use App\Enums\LandUnit;
use App\Models\City;
use App\Models\LandRefSequence;
use App\Models\Park;
use App\Models\Plot;
use App\Models\Setting;
use App\Models\State;
use App\Models\Taluka;
use Illuminate\Support\Facades\DB;

/**
 * Generates the immutable Land Reference Number, e.g. BHV-GJ-BHV-TLJ-0001.
 * Format, padding and sequence scope come from settings (fallback: config/solar.php).
 */
class LandRefService
{
    public function format(): string
    {
        return (string) Setting::get('land_ref.format', config('solar.land_ref.format'));
    }

    public function padding(): int
    {
        return max(1, (int) Setting::get('land_ref.seq_pad', config('solar.land_ref.seq_pad')));
    }

    public function scope(): string
    {
        return (string) Setting::get('land_ref.seq_scope', config('solar.land_ref.seq_scope'));
    }

    /** Token values for a park + location. */
    public function tokens(Park $park, State $state, City $city, Taluka $taluka): array
    {
        return [
            '{PARK}' => strtoupper($park->park_code),
            '{STATE}' => strtoupper($state->code),
            '{CITY}' => strtoupper($city->code),
            '{TALUKA}' => strtoupper($taluka->code),
        ];
    }

    public function scopeKey(array $tokens): string
    {
        if ($this->scope() === 'park') {
            return 'PARK:'.$tokens['{PARK}'];
        }

        return strtr(str_replace('{SEQ}', '', $this->format()), $tokens);
    }

    public function build(array $tokens, int $seq): string
    {
        return strtr($this->format(), $tokens + ['{SEQ}' => str_pad((string) $seq, $this->padding(), '0', STR_PAD_LEFT)]);
    }

    /** What the next number would be (no increment). Used for the live preview in the form. */
    public function preview(Park $park, State $state, City $city, Taluka $taluka): string
    {
        $tokens = $this->tokens($park, $state, $city, $taluka);
        $last = LandRefSequence::where('scope_key', $this->scopeKey($tokens))->value('last_number') ?? 0;

        return $this->build($tokens, $last + 1);
    }

    /**
     * Reserve the next number. MUST be called inside a DB transaction:
     * the sequence row is locked until the transaction commits.
     */
    public function next(Park $park, State $state, City $city, Taluka $taluka): string
    {
        $tokens = $this->tokens($park, $state, $city, $taluka);
        $key = $this->scopeKey($tokens);

        LandRefSequence::firstOrCreate(['scope_key' => $key], ['last_number' => 0]);
        $seq = LandRefSequence::where('scope_key', $key)->lockForUpdate()->first();

        do {
            $seq->last_number++;
            $ref = $this->build($tokens, $seq->last_number);
        } while (Plot::withTrashed()->where('land_ref_no', $ref)->exists());

        $seq->save();

        return $ref;
    }

    /** Create a plot with a freshly generated Land Reference Number in one transaction. */
    public function createPlot(array $data): Plot
    {
        return DB::transaction(function () use ($data) {
            $plot = new Plot($data);
            $plot->area_acres = LandUnit::acres($data['land_unit'], (float) $data['total_area']);
            $plot->land_ref_no = $this->next(
                Park::findOrFail($data['park_id']),
                State::findOrFail($data['state_id']),
                City::findOrFail($data['city_id']),
                Taluka::findOrFail($data['taluka_id'])
            );
            $plot->save();

            return $plot;
        });
    }
}
