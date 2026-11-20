<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/** Production seed: roles, location masters and the Super Admin only. */
class BaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([RoleSeeder::class, LocationSeeder::class, UserSeeder::class]);
    }
}
