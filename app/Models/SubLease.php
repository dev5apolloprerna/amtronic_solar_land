<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUserStamps;
use App\Models\Concerns\VisibleBySpv;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubLease extends Model
{
    use SoftDeletes, HasUserStamps, Auditable, VisibleBySpv;

    protected string $auditModule = 'sublease';

    protected $fillable = ['lease_id', 'park_id', 'spv_id', 'party_id', 'capacity_mw', 'start_date', 'end_date', 'rent_amount', 'rent_frequency', 'payment_terms',
        'agreement_no', 'lender_noc_obtained', 'status'];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date', 'capacity_mw' => 'decimal:3', 'rent_amount' => 'decimal:2'];

    public function auditLabel(): string
    {
        return (string) $this->sub_lease_no;
    }

    public function lease(): BelongsTo { return $this->belongsTo(Lease::class); }
    public function park(): BelongsTo { return $this->belongsTo(Park::class); }
    public function spv(): BelongsTo { return $this->belongsTo(Spv::class); }
    public function party(): BelongsTo { return $this->belongsTo(Party::class); }
    public function documents(): MorphMany { return $this->morphMany(Document::class, 'documentable'); }

    public static function nextNumber(): string
    {
        $last = static::withTrashed()->max('id') ?? 0;

        return 'SUB'.str_pad((string) ($last + 1), 5, '0', STR_PAD_LEFT);
    }
}
