<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasCode;
use App\Models\Concerns\HasUserStamps;
use App\Models\Concerns\VisibleBySpv;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use LogicException;

/** LAND_MASTER: one land parcel. Land reference no. = SPV-STATE-PARK-PARCEL-YEAR, never changes. */
class Land extends Model
{
    use SoftDeletes, HasUserStamps, Auditable, HasCode, VisibleBySpv;

    public const CODE_PREFIX = 'LND';
    public const CODE_PAD = 3;
    protected string $auditModule = 'land';

    protected $fillable = ['park_id', 'spv_id', 'state_id', 'district_id', 'city_id', 'parcel_code', 'ref_year', 'area_sqm_712', 'area_acres_712',
        'area_under_lease_acres', 'dlr_sqm', 'rate_per_acre', 'increment_after_yrs', 'increment_pct', 'title_report', 'title_dd_status',
        'lessor_capacity_verified', 'encumbrance_status', 'lien_details', 'na_application', 'na_status', 'land_status',
        'connectivity_mw', 'substation', 'aggregator', 'tfr_confirmation'];

    protected $casts = ['area_sqm_712' => 'decimal:2', 'area_acres_712' => 'decimal:2', 'area_under_lease_acres' => 'decimal:2', 'dlr_sqm' => 'decimal:2',
        'rate_per_acre' => 'decimal:2', 'increment_pct' => 'decimal:2', 'connectivity_mw' => 'decimal:3'];

    protected static function booted(): void
    {
        static::updating(function (Land $land) {
            foreach (['land_ref_no', 'parcel_code', 'ref_year'] as $locked) {
                if ($land->isDirty($locked)) {
                    throw new LogicException('Land reference number, parcel code and reference year cannot change after saving.');
                }
            }
        });
    }

    public function auditLabel(): string { return (string) $this->land_ref_no; }

    public function park(): BelongsTo { return $this->belongsTo(Park::class); }
    public function spv(): BelongsTo { return $this->belongsTo(Spv::class); }
    public function state(): BelongsTo { return $this->belongsTo(State::class); }
    public function district(): BelongsTo { return $this->belongsTo(District::class); }
    public function city(): BelongsTo { return $this->belongsTo(City::class); }
    public function surveys(): HasMany { return $this->hasMany(LandSurvey::class)->orderBy('id'); }
    public function farmers(): HasMany { return $this->hasMany(LandOwner::class)->orderBy('id'); }
    public function leases(): HasMany { return $this->hasMany(Lease::class); }
    public function activeLease(): HasOne { return $this->hasOne(Lease::class)->where('lease_status', 'Active'); }
    public function schedules(): HasMany { return $this->hasMany(RentSchedule::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
    public function subleases(): HasMany { return $this->hasMany(Sublease::class); }
    public function compliance(): HasOne { return $this->hasOne(Compliance::class); }
    public function documents(): HasMany { return $this->hasMany(Document::class); }

    public function ownershipTotal(): float
    {
        return round((float) $this->farmers->sum('ownership_pct'), 4);
    }
}
