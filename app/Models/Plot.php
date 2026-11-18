<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUserStamps;
use App\Models\Concerns\VisibleBySpv;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use LogicException;

/** One survey number. Carries the immutable Land Reference Number. */
class Plot extends Model
{
    use SoftDeletes, HasUserStamps, Auditable, VisibleBySpv;

    protected string $auditModule = 'plot';

    protected $fillable = ['park_id', 'spv_id', 'state_id', 'city_id', 'taluka_id', 'village', 'survey_no', 'sub_survey_no', 'total_area', 'land_unit', 'area_acres',
        'area_under_lease_acres', 'dlr_area_sqm', 'rate_per_acre', 'increment_after_yrs', 'increment_pct', 'title_report', 'title_dd_status',
        'lessor_capacity_verified', 'encumbrance_status', 'lien_details', 'na_application', 'na_status', 'land_status'];
    // land_ref_no is deliberately NOT fillable: only LandRefService sets it.

    protected $casts = ['total_area' => 'decimal:4', 'area_acres' => 'decimal:4', 'area_under_lease_acres' => 'decimal:4', 'rate_per_acre' => 'decimal:2',
        'increment_pct' => 'decimal:2', 'dlr_area_sqm' => 'decimal:2'];

    protected static function booted(): void
    {
        static::updating(function (Plot $plot) {
            if ($plot->isDirty('land_ref_no')) {
                throw new LogicException('Land Reference Number cannot be changed once generated.');
            }
        });
    }

    public function auditLabel(): string
    {
        return (string) $this->land_ref_no;
    }

    public function park(): BelongsTo { return $this->belongsTo(Park::class); }
    public function spv(): BelongsTo { return $this->belongsTo(Spv::class); }
    public function state(): BelongsTo { return $this->belongsTo(State::class); }
    public function city(): BelongsTo { return $this->belongsTo(City::class); }
    public function taluka(): BelongsTo { return $this->belongsTo(Taluka::class); }

    public function ownershipVersions(): HasMany { return $this->hasMany(PlotOwnershipVersion::class)->orderByDesc('version_no'); }
    public function grid(): HasOne { return $this->hasOne(PlotGridConnectivity::class); }
    public function compliance(): HasOne { return $this->hasOne(ComplianceRecord::class); }
    public function leases(): HasMany { return $this->hasMany(Lease::class); }
    public function rentSchedules(): HasMany { return $this->hasMany(RentSchedule::class); }
    public function documents(): HasMany { return $this->hasMany(Document::class); }

    public function currentOwnership(): HasOne
    {
        return $this->hasOne(PlotOwnershipVersion::class)->where('status', 'Finalised')->whereNull('effective_to');
    }

    public function draftOwnership(): HasOne
    {
        return $this->hasOne(PlotOwnershipVersion::class)->where('status', 'Draft');
    }

    public function activeLease(): HasOne
    {
        return $this->hasOne(Lease::class)->where('status', 'Active');
    }

    public function surveyLabel(): string
    {
        return $this->survey_no.($this->sub_survey_no !== '' && $this->sub_survey_no !== null ? '/'.$this->sub_survey_no : '');
    }
}
