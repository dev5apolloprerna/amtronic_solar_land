<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlotOwnershipShare extends Model
{
    use HasUserStamps, Auditable;

    protected string $auditModule = 'ownership';

    protected $fillable = ['ownership_version_id', 'party_id', 'share_percent'];

    protected $casts = ['share_percent' => 'decimal:4'];

    public function auditLabel(): string
    {
        return 'v'.$this->ownership_version_id.' · '.($this->party?->party_name ?? $this->party_id);
    }

    public function version(): BelongsTo { return $this->belongsTo(PlotOwnershipVersion::class, 'ownership_version_id'); }
    public function party(): BelongsTo { return $this->belongsTo(Party::class); }
}
