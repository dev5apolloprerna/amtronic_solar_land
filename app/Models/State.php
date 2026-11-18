<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    use SoftDeletes, HasUserStamps, Auditable;

    protected string $auditModule = 'state';

    protected $fillable = ['name', 'code', 'status'];

    public function auditLabel(): string
    {
        return $this->name;
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function plots(): HasMany
    {
        return $this->hasMany(Plot::class);
    }

    /** Code is locked once any plot uses this state. */
    public function codeLocked(): bool
    {
        return $this->exists && $this->plots()->withTrashed()->exists();
    }
}
