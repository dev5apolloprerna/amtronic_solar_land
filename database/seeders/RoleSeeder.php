<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

/** The four roles of the HTML template with the same permission strings (v=view a=add e=edit d=delete x=export). */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $all = fn (string $p) => array_fill_keys(array_keys(config('solar.modules')), $p);

        $roles = [
            ['Super Admin', 'Full access. Manages users, roles and SPV access.', true, $all('vaedx')],
            ['Land Manager', 'Creates and maintains land, lease, sublease, compliance, documents and location masters.', false, [
                'state' => 'vae', 'district' => 'vae', 'city' => 'vae', 'spv' => 'v', 'park' => 'vae', 'land' => 'vaex', 'survey' => 'vaedx', 'farmer' => 'vaedx',
                'lease' => 'vaex', 'schedule' => 'v', 'payment' => 'v', 'sublease' => 'vaex', 'compliance' => 'vaex', 'doc' => 'vaedx', 'report' => 'vx', 'audit' => 'v',
            ]],
            ['Finance', 'Maintains rent schedule and records payments.', false, [
                'state' => 'v', 'district' => 'v', 'city' => 'v', 'spv' => 'v', 'park' => 'v', 'land' => 'v', 'survey' => 'v', 'farmer' => 'v',
                'lease' => 'vx', 'schedule' => 'vex', 'payment' => 'vaex', 'sublease' => 'v', 'compliance' => 'v', 'doc' => 'va', 'report' => 'vx', 'audit' => 'v',
            ]],
            ['Auditor', 'Read-only access to everything, with export.', false, $all('vx')],
        ];

        foreach ($roles as [$name, $desc, $super, $perms]) {
            $role = Role::updateOrCreate(['name' => $name], ['description' => $desc, 'is_super' => $super]);
            foreach (config('solar.modules') as $module => $meta) {
                $p = $perms[$module] ?? '';
                $row = [];
                foreach (['view' => 'v', 'add' => 'a', 'edit' => 'e', 'delete' => 'd', 'export' => 'x'] as $act => $ch) {
                    $row['can_'.$act] = str_contains($p, $ch) && ! in_array($act, $meta['na'], true);
                }
                $role->permissions()->updateOrCreate(['module' => $module], $row);
            }
        }
    }
}
