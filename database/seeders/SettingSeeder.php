<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'land_ref.format' => [config('solar.land_ref.format'), 'Land Reference Number format'],
            'land_ref.seq_pad' => [(string) config('solar.land_ref.seq_pad'), 'Digits in the sequence'],
            'land_ref.seq_scope' => [config('solar.land_ref.seq_scope'), 'prefix = per park+location, park = one per park'],
        ] as $key => [$value, $desc]) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value, 'description' => $desc]);
        }
    }
}
