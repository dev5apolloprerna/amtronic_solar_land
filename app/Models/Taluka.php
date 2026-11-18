<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Taluka extends Model
{
    use SoftDeletes, HasUserStamps, Auditable;

    protected string $auditModule = 'taluka';

    protected $fillable = ['city_id', 'name', 'code', 'status'];

    public function auditLabel(): string
    {
        return $this->name;
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
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
