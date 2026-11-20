<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasCode;
use App\Models\Concerns\HasUserStamps;
use App\Models\Concerns\VisibleBySpv;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/** SUBLEASE_MASTER: area (acres) and capacity (MW) carved out of a lease */
class Sublease extends Model
{
    use SoftDeletes, HasUserStamps, Auditable, HasCode, VisibleBySpv;

    public const CODE_PREFIX = 'SUB';
    public const CODE_PAD = 3;
    protected string $auditModule = 'sublease';
    protected ?string $spvPath = 'land';
    protected $fillable = ['lease_id', 'land_id', 'sublessee_name', 'sublessee_pan', 'sublease_area_acres', 'capacity_mw', 'sublease_rent', 'sublease_start',
        'sublease_end', 'agreement_no', 'lender_noc_obtained', 'status'];
    protected $casts = ['sublease_start' => 'date', 'sublease_end' => 'date', 'sublease_area_acres' => 'decimal:2', 'capacity_mw' => 'decimal:3', 'sublease_rent' => 'decimal:2'];

    public function lease(): BelongsTo { return $this->belongsTo(Lease::class); }
    public function land(): BelongsTo { return $this->belongsTo(Land::class); }
}
