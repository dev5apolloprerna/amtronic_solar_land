<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/** Roles and the module × action permission matrix (Super Admin only). */
class RoleController extends Controller
{
    public function create()
    {
        return $this->drawer('Add role', 'ROLES', 'users.role-form', ['role' => new Role()], module: 'role');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:80', Rule::unique('roles', 'name')], 'description' => ['nullable', 'string', 'max:255']]);
        $role = Role::create($data + ['is_super' => false]);
        app(AuditService::class)->log('role', 'CREATE', $role->id, $role->name, null, null, 'New role');

        return $this->ok("Role {$role->name} created · set its permissions next", route('users.index', ['tab' => 'roles', 'role' => $role->id]));
    }

    public function edit(Role $role)
    {
        return $this->drawer('Rename role', 'ROLES · '.$role->name, 'users.role-form', compact('role'), $role->is_super, 'role');
    }

    public function update(Request $request, Role $role)
    {
        abort_if($role->is_super, 422, 'The Super Admin role cannot be changed.');
        $data = $request->validate(['name' => ['required', 'string', 'max:80', Rule::unique('roles', 'name')->ignore($role->id)], 'description' => ['nullable', 'string', 'max:255']]);
        foreach ($data as $k => $v) {
            if ($role->$k !== $v) {
                app(AuditService::class)->log('role', 'UPDATE', $role->id, $role->name, $k, $role->$k, $v);
            }
        }
        $role->update($data);

        return $this->ok("Role {$role->name} saved", route('users.index', ['tab' => 'roles', 'role' => $role->id]));
    }

    /** Save the permission matrix. Turning off View turns off everything else for that module. */
    public function permissions(Request $request, Role $role)
    {
        if ($role->is_super) {
            return $this->refuse('Super Admin has every permission. These cannot be changed.');
        }
        $input = $request->input('perm', []);
        $before = $role->matrix();
        $changes = 0;

        DB::transaction(function () use ($role, $input, $before, &$changes) {
            foreach (config('solar.modules') as $module => $meta) {
                $row = [];
                foreach (config('solar.actions') as $act) {
                    $row[$act] = ! in_array($act, $meta['na'], true) && ! empty($input[$module][$act]);
                }
                if (in_array(true, array_diff_key($row, ['view' => 1]), true)) {
                    $row['view'] = true; // any action needs view
                }
                foreach ($row as $act => $val) {
                    if (($before[$module][$act] ?? false) !== $val) {
                        app(AuditService::class)->log('role', 'UPDATE', $role->id, $role->name, "$module.$act", $before[$module][$act] ? 'allowed' : 'denied', $val ? 'allowed' : 'denied');
                        $changes++;
                    }
                }
                $role->permissions()->updateOrCreate(['module' => $module], collect($row)->mapWithKeys(fn ($v, $k) => ['can_'.$k => $v])->all());
            }
        });

        return $this->ok("$changes permission".($changes === 1 ? '' : 's')." updated for {$role->name}", route('users.index', ['tab' => 'roles', 'role' => $role->id]));
    }

    public function destroy(Role $role)
    {
        if ($role->is_super) {
            return $this->refuse('The Super Admin role cannot be deleted.');
        }
        if ($role->users()->withTrashed()->exists()) {
            return $this->refuse("Users are assigned to {$role->name}. Move them to another role first.");
        }
        app(AuditService::class)->log('role', 'DELETE', $role->id, $role->name, null, 'role #'.$role->id, null);
        $role->delete();

        return $this->ok("Role {$role->name} deleted", route('users.index', ['tab' => 'roles']));
    }
}
