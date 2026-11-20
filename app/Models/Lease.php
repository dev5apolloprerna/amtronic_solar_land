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

/** LEASE_MASTER */
class Lease extends Model
{
    use SoftDeletes, HasUserStamps, Auditable, HasCode, VisibleBySpv;

    public const CODE_PREFIX = 'LSE';
    public const CODE_PAD = 3;
    protected string $auditModule = 'lease';
    protected ?string $spvPath = 'land';

    protected $fillable = ['land_id', 'lessor_name', 'lessor_pan', 'leased_area_acres', 'leased_capacity_mw', 'lease_status', 'lessor_address', 'lessor_state_id',
        'lessor_district_id', 'lessor_city_id', 'lessor_pincode', 'atl_date', 'lease_date', 'lease_start_date', 'lease_duration_mths', 'lease_end_date', 'uni_number',
        'sro_doc_no', 'agreement_no', 'mutation_entry_no', 'rent_rate_per_acre', 'rent_frequency', 'rent_amount', 'security_deposit', 'escalation_pct',
        'escalation_every_yrs', 'tds_applicable', 'tds_rate', 'gst_applicable'];

    protected $casts = ['atl_date' => 'date', 'lease_date' => 'date', 'lease_start_date' => 'date', 'lease_end_date' => 'date', 'leased_area_acres' => 'decimal:2',
        'leased_capacity_mw' => 'decimal:3', 'rent_rate_per_acre' => 'decimal:2', 'rent_amount' => 'decimal:2', 'security_deposit' => 'decimal:2',
        'escalation_pct' => 'decimal:2', 'tds_rate' => 'decimal:2'];

    public function land(): BelongsTo { return $this->belongsTo(Land::class); }
    public function lessorState(): BelongsTo { return $this->belongsTo(State::class, 'lessor_state_id'); }
    public function lessorDistrict(): BelongsTo { return $this->belongsTo(District::class, 'lessor_district_id'); }
    public function lessorCity(): BelongsTo { return $this->belongsTo(City::class, 'lessor_city_id'); }
    public function schedules(): HasMany { return $this->hasMany(RentSchedule::class)->orderBy('due_date'); }
    public function subleases(): HasMany { return $this->hasMany(Sublease::class); }
}
