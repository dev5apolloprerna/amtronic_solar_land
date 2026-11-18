<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUserStamps;
use App\Models\Concerns\VisibleBySpv;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComplianceRecord extends Model
{
    use SoftDeletes, HasUserStamps, Auditable, VisibleBySpv;

    protected string $auditModule = 'compliance';
    protected ?string $spvPath = 'plot';

    protected $fillable = ['plot_id', 'mortgage_created', 'mortgage_date', 'lender_name', 'cersai_reg_no', 'original_docs_custody', 'custodian_name',
        'docs_deposit_date', 'lender_noc_status', 'pdc_open_items', 'target_closure_date', 'escalation_level', 'remarks'];

    protected $casts = ['mortgage_date' => 'date', 'docs_deposit_date' => 'date', 'target_closure_date' => 'date'];

    public function auditLabel(): string
    {
        return 'CMP'.str_pad((string) $this->id, 3, '0', STR_PAD_LEFT);
    }

    public function plot(): BelongsTo { return $this->belongsTo(Plot::class); }
    public function documents(): MorphMany { return $this->morphMany(Document::class, 'documentable'); }

    public function isOverdue(): bool
    {
        return $this->pdc_open_items > 0 && $this->target_closure_date && $this->target_closure_date->isPast();
    }
}
