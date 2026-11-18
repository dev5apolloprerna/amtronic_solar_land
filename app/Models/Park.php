<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Park extends Model
{
    use SoftDeletes, HasUserStamps, Auditable;

    protected string $auditModule = 'park';

    protected $fillable = ['park_code', 'park_name', 'state_id', 'city_id', 'taluka_id', 'address', 'total_land_area', 'land_unit', 'total_land_acres',
        'installed_capacity_mw', 'description', 'status'];

    protected $casts = ['total_land_area' => 'decimal:4', 'total_land_acres' => 'decimal:4', 'installed_capacity_mw' => 'decimal:3'];

    public function auditLabel(): string
    {
        return $this->park_code;
    }

    public function state(): BelongsTo { return $this->belongsTo(State::class); }
    public function city(): BelongsTo { return $this->belongsTo(City::class); }
    public function taluka(): BelongsTo { return $this->belongsTo(Taluka::class); }
    public function spvs(): HasMany { return $this->hasMany(Spv::class); }
    public function plots(): HasMany { return $this->hasMany(Plot::class); }
    public function leases(): HasMany { return $this->hasMany(Lease::class); }
    public function subLeases(): HasMany { return $this->hasMany(SubLease::class); }
}
