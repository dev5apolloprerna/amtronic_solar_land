<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlotGridConnectivity extends Model
{
    use HasUserStamps, Auditable;

    protected $table = 'plot_grid_connectivity';
    protected string $auditModule = 'grid';

    protected $fillable = ['plot_id', 'connectivity_mw', 'substation', 'aggregator', 'tfr_confirmation'];

    protected $casts = ['connectivity_mw' => 'decimal:3'];

    public function auditLabel(): string
    {
        return (string) ($this->plot?->land_ref_no ?? $this->plot_id);
    }

    public function plot(): BelongsTo { return $this->belongsTo(Plot::class); }
}
