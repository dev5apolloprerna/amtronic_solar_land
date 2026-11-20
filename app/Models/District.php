<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/** DISTRICT_MASTER */
class District extends Model
{
    use SoftDeletes, HasUserStamps, Auditable;

    protected string $auditModule = 'district';
    protected $fillable = ['state_id', 'district_name'];

    public function auditLabel(): string { return $this->district_name; }
    public function state(): BelongsTo { return $this->belongsTo(State::class); }
    public function cities(): HasMany { return $this->hasMany(City::class); }
    public function lands(): HasMany { return $this->hasMany(Land::class); }
}
