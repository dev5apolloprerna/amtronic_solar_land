<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/** Production seed: roles, locations, settings and the Super Admin only. */
class BaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([RoleSeeder::class, LocationSeeder::class, SettingSeeder::class, UserSeeder::class]);
    }
}
