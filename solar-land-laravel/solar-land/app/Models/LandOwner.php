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

/** LAND_OWNER: farmer / co-owner of a parcel with ownership (rent share) % */
class LandOwner extends Model
{
    use SoftDeletes, HasUserStamps, Auditable, HasCode, VisibleBySpv;

    public const CODE_PREFIX = 'OWN';
    public const CODE_PAD = 3;
    protected string $auditModule = 'farmer';
    protected ?string $spvPath = 'land';
    protected $fillable = ['land_id', 'owner_name', 'ownership_pct', 'owner_pan', 'aadhaar', 'voter_id', 'bank_ac', 'bank_branch'];
    protected $casts = ['ownership_pct' => 'decimal:4', 'bank_ac' => 'encrypted'];

    public function auditLabel(): string { return $this->code.' · '.$this->owner_name; }
    public function land(): BelongsTo { return $this->belongsTo(Land::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class, 'farmer_id'); }

    public function maskedBank(): string
    {
        $ac = (string) $this->bank_ac;

        return $ac === '' ? '—' : str_repeat('X', max(0, strlen($ac) - 4)).substr($ac, -4);
    }
}
