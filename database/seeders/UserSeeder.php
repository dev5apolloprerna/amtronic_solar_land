<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Sign-in accounts. CHANGE THESE PASSWORDS after the first login.
 * superadmin / Admin@12345 (all SPVs). Demo users are added by DemoDataSeeder.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['username' => 'superadmin'], [
            'name' => 'Super Admin',
            'email' => env('ADMIN_EMAIL', 'admin@example.com'),
            'password' => env('ADMIN_PASSWORD', 'Admin@12345'),
            'role_id' => Role::where('is_super', true)->value('id'),
            'is_active' => true,
        ]);
    }
}
