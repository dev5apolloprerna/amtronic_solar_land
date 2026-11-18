<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUserStamps;
use App\Models\Concerns\VisibleBySpv;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Spv extends Model
{
    use SoftDeletes, HasUserStamps, Auditable, VisibleBySpv;

    protected string $auditModule = 'spv';
    protected ?string $spvPath = 'self';

    protected $fillable = ['spv_code', 'spv_name', 'park_id', 'party_id', 'cin', 'incorporation_date', 'registered_address', 'contact_person', 'mobile', 'email',
        'technology', 'capacity_mwp', 'project_status', 'status'];

    protected $casts = ['incorporation_date' => 'date', 'capacity_mwp' => 'decimal:3'];

    public function auditLabel(): string
    {
        return $this->spv_code;
    }

    public function park(): BelongsTo { return $this->belongsTo(Park::class); }
    public function party(): BelongsTo { return $this->belongsTo(Party::class); }
    public function plots(): HasMany { return $this->hasMany(Plot::class); }
    public function leases(): HasMany { return $this->hasMany(Lease::class); }
}
