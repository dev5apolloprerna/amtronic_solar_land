<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/** CITY_MASTER: city, taluka or tehsil */
class City extends Model
{
    use SoftDeletes, HasUserStamps, Auditable;

    protected string $auditModule = 'city';
    protected $fillable = ['state_id', 'district_id', 'city_name', 'city_type'];

    public function auditLabel(): string { return $this->city_name; }
    public function state(): BelongsTo { return $this->belongsTo(State::class); }
    public function district(): BelongsTo { return $this->belongsTo(District::class); }
    public function lands(): HasMany { return $this->hasMany(Land::class); }
}
