<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            LocationSeeder::class,
            SettingSeeder::class,
            UserSeeder::class,
        ]);

        // Sample parks, SPVs, parties, survey numbers, leases, rent and sub-leases (the spec's 200 MW example).
        // Skip on a production install with: php artisan db:seed --class=Database\\Seeders\\BaseSeeder
        if (! app()->isProduction()) {
            $this->call(DemoDataSeeder::class);
        }
    }
}
