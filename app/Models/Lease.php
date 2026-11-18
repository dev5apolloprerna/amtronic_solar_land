<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUserStamps;
use App\Models\Concerns\VisibleBySpv;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lease extends Model
{
    use SoftDeletes, HasUserStamps, Auditable, VisibleBySpv;

    protected string $auditModule = 'lease';

    protected $fillable = ['lease_date', 'park_id', 'spv_id', 'plot_id', 'atl_date', 'registration_date', 'lease_start_date', 'lease_duration_months', 'lease_end_date',
        'total_lease_area', 'land_unit', 'lease_area_acres', 'leased_capacity_mw', 'rent_type', 'rent_rate', 'annual_rent', 'payment_frequency', 'escalation_pct',
        'escalation_every_yrs', 'security_deposit', 'tds_applicable', 'tds_rate', 'gst_applicable', 'uni_number', 'sro_doc_no', 'agreement_no', 'mutation_entry_no', 'status'];

    protected $casts = ['lease_date' => 'date', 'atl_date' => 'date', 'registration_date' => 'date', 'lease_start_date' => 'date', 'lease_end_date' => 'date',
        'total_lease_area' => 'decimal:4', 'lease_area_acres' => 'decimal:4', 'leased_capacity_mw' => 'decimal:3', 'rent_rate' => 'decimal:2',
        'annual_rent' => 'decimal:2', 'escalation_pct' => 'decimal:2', 'security_deposit' => 'decimal:2', 'tds_rate' => 'decimal:2'];

    public function auditLabel(): string
    {
        return (string) $this->lease_no;
    }

    public function park(): BelongsTo { return $this->belongsTo(Park::class); }
    public function spv(): BelongsTo { return $this->belongsTo(Spv::class); }
    public function plot(): BelongsTo { return $this->belongsTo(Plot::class); }
    public function schedules(): HasMany { return $this->hasMany(RentSchedule::class)->orderBy('installment_no'); }
    public function subLeases(): HasMany { return $this->hasMany(SubLease::class); }
    public function documents(): MorphMany { return $this->morphMany(Document::class, 'documentable'); }

    public function isActive(): bool
    {
        return $this->status === 'Active';
    }

    public static function nextNumber(): string
    {
        $last = static::withTrashed()->max('id') ?? 0;

        return 'LSE'.str_pad((string) ($last + 1), 5, '0', STR_PAD_LEFT);
    }
}
