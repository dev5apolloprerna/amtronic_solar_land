<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasCode;
use App\Models\Concerns\HasUserStamps;
use App\Models\Concerns\VisibleBySpv;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/** LAND_SURVEY: survey / plot numbers (7/12 extracts) of a parcel */
class LandSurvey extends Model
{
    use SoftDeletes, HasUserStamps, Auditable, HasCode, VisibleBySpv;

    public const CODE_PREFIX = 'SRV';
    public const CODE_PAD = 3;
    protected string $auditModule = 'survey';
    protected ?string $spvPath = 'land';
    protected $fillable = ['land_id', 'state_id', 'district_id', 'city_id', 'village', 'survey_plot_no', 'area_sqm', 'area_acres'];
    protected $casts = ['area_sqm' => 'decimal:2', 'area_acres' => 'decimal:2'];

    public function land(): BelongsTo { return $this->belongsTo(Land::class); }
    public function state(): BelongsTo { return $this->belongsTo(State::class); }
    public function district(): BelongsTo { return $this->belongsTo(District::class); }
    public function city(): BelongsTo { return $this->belongsTo(City::class); }
}
