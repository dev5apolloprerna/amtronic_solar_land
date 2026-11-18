<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use SoftDeletes, HasUserStamps, Auditable;

    protected string $auditModule = 'city';

    protected $fillable = ['state_id', 'name', 'code', 'status'];

    public function auditLabel(): string
    {
        return $this->name;
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function talukas(): HasMany
    {
        return $this->hasMany(Taluka::class);
    }

    public function plots(): HasMany
    {
        return $this->hasMany(Plot::class);
    }

    public function codeLocked(): bool
    {
        return $this->exists && $this->plots()->withTrashed()->exists();
    }
}
