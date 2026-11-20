<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'description', 'is_super'];

    protected $casts = ['is_super' => 'boolean'];

    public function permissions(): HasMany
    {
        return $this->hasMany(RolePermission::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /** ['lease' => ['view'=>true,...], ...] for every configured module. */
    public function matrix(): array
    {
        $rows = $this->permissions->keyBy('module');
        $out = [];
        foreach (array_keys(config('solar.modules')) as $module) {
            foreach (config('solar.actions') as $act) {
                $out[$module][$act] = $this->is_super || (bool) ($rows[$module]->{'can_'.$act} ?? false);
            }
        }

        return $out;
    }
}
