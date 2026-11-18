<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlotOwnershipVersion extends Model
{
    use HasUserStamps, Auditable;

    protected string $auditModule = 'ownership';

    protected $fillable = ['plot_id', 'version_no', 'effective_from', 'effective_to', 'status', 'total_percent', 'change_reason', 'finalised_by', 'finalised_at'];

    protected $casts = ['effective_from' => 'date', 'effective_to' => 'date', 'finalised_at' => 'datetime', 'total_percent' => 'decimal:4'];

    public function auditLabel(): string
    {
        return ($this->plot?->land_ref_no ?? 'plot '.$this->plot_id).' v'.$this->version_no;
    }

    public function plot(): BelongsTo { return $this->belongsTo(Plot::class); }
    public function shares(): HasMany { return $this->hasMany(PlotOwnershipShare::class, 'ownership_version_id'); }
    public function finaliser(): BelongsTo { return $this->belongsTo(User::class, 'finalised_by'); }

    public function isDraft(): bool
    {
        return $this->status === 'Draft';
    }

    public function recalcTotal(): void
    {
        $this->total_percent = round((float) $this->shares()->sum('share_percent'), 4);
        $this->saveQuietly();
    }
}
