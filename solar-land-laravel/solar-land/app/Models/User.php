<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUserStamps;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/** USER_MASTER */
class User extends Authenticatable
{
    use Notifiable, SoftDeletes, HasUserStamps, Auditable;

    protected string $auditModule = 'user';

    protected $fillable = ['name', 'username', 'email', 'password', 'role_id', 'is_active', 'is_locked', 'failed_attempts', 'last_login_at', 'last_login_ip'];

    protected $hidden = ['password', 'remember_token'];

    private ?array $spvIdCache = null;
    private ?array $permCache = null;

    protected function casts(): array
    {
        return ['password' => 'hashed', 'is_active' => 'boolean', 'is_locked' => 'boolean', 'last_login_at' => 'datetime', 'email_verified_at' => 'datetime'];
    }

    public function auditLabel(): string
    {
        return $this->username;
    }

    public function role(): BelongsTo { return $this->belongsTo(Role::class); }

    public function spvs(): BelongsToMany { return $this->belongsToMany(Spv::class, 'user_spv_access')->withTimestamps(); }

    public function isSuper(): bool
    {
        return (bool) $this->role?->is_super;
    }

    public function spvIds(): array
    {
        return $this->spvIdCache ??= $this->spvs()->pluck('spvs.id')->all();
    }

    public function canAccessSpv(?int $spvId): bool
    {
        return $this->isSuper() || in_array($spvId, $this->spvIds(), true);
    }

    public function hasPermission(string $module, string $action = 'view'): bool
    {
        if ($this->isSuper()) {
            return true;
        }
        if (in_array($module, ['user', 'role'], true)) {
            return false;
        }
        $this->permCache ??= $this->role?->permissions->keyBy('module')->all() ?? [];

        return (bool) (($this->permCache[$module] ?? null)?->{'can_'.$action} ?? false);
    }

    public function initials(): string
    {
        return collect(explode(' ', $this->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('');
    }
}
