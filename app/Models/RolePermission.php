<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePermission extends Model
{
    protected $fillable = ['role_id', 'module', 'can_view', 'can_add', 'can_edit', 'can_delete', 'can_export'];

    protected $casts = ['can_view' => 'boolean', 'can_add' => 'boolean', 'can_edit' => 'boolean', 'can_delete' => 'boolean', 'can_export' => 'boolean'];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
