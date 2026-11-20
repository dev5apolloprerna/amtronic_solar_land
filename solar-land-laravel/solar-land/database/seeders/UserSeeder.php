<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Super Admin sign-in (all SPVs). CHANGE THE PASSWORD after the first login.
 * Default: superadmin / Demo@123, or ADMIN_EMAIL / ADMIN_PASSWORD from .env.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['username' => 'superadmin'], [
            'name' => 'Nikhil Shah',
            'email' => env('ADMIN_EMAIL', 'nikhil.shah@example.com'),
            'password' => env('ADMIN_PASSWORD', 'Demo@123'),
            'role_id' => Role::where('is_super', true)->value('id'),
            'is_active' => true,
        ]);
    }
}
