<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasCode;
use App\Models\Concerns\HasUserStamps;
use App\Models\Concerns\VisibleBySpv;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/** SPV_MASTER */
class Spv extends Model
{
    use SoftDeletes, HasUserStamps, Auditable, HasCode, VisibleBySpv;

    public const CODE_PREFIX = 'SPV';
    public const CODE_PAD = 3;
    protected string $auditModule = 'spv';
    protected ?string $spvPath = 'self';
    protected $fillable = ['state_id', 'park_id', 'spv_code', 'spv_name', 'technology', 'capacity_mwp', 'status'];
    protected $casts = ['capacity_mwp' => 'decimal:3'];

    public function auditLabel(): string { return $this->spv_code; }
    public function state(): BelongsTo { return $this->belongsTo(State::class); }
    public function park(): BelongsTo { return $this->belongsTo(Park::class); }
    public function lands(): HasMany { return $this->hasMany(Land::class); }
}
