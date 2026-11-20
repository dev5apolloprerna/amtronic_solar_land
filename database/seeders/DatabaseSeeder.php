<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([RoleSeeder::class, LocationSeeder::class, UserSeeder::class]);

        // Sample parks, SPVs, land parcels, farmers, leases, rent, payments, subleases, compliance and documents (the template's sample data).
        // Skip on a production install with: php artisan db:seed --class=Database\\Seeders\\BaseSeeder
        if (! app()->isProduction()) {
            $this->call(DemoDataSeeder::class);
        }
    }
}
