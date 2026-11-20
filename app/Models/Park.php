<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasCode;
use App\Models\Concerns\HasUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/** PARK_MASTER */
class Park extends Model
{
    use SoftDeletes, HasUserStamps, Auditable, HasCode;

    public const CODE_PREFIX = 'PRK';
    public const CODE_PAD = 3;
    protected string $auditModule = 'park';
    protected $fillable = ['park_code', 'park_name', 'state_id', 'district_id', 'city_id', 'total_area_acres', 'installed_capacity_mw'];
    protected $casts = ['total_area_acres' => 'decimal:2', 'installed_capacity_mw' => 'decimal:3'];

    public function auditLabel(): string { return $this->park_code; }
    public function state(): BelongsTo { return $this->belongsTo(State::class); }
    public function district(): BelongsTo { return $this->belongsTo(District::class); }
    public function city(): BelongsTo { return $this->belongsTo(City::class); }
    public function spvs(): HasMany { return $this->hasMany(Spv::class); }
    public function lands(): HasMany { return $this->hasMany(Land::class); }
}
