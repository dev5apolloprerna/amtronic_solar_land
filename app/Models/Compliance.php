<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasCode;
use App\Models\Concerns\HasUserStamps;
use App\Models\Concerns\VisibleBySpv;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/** COMPLIANCE_TRACKER: one record per land parcel */
class Compliance extends Model
{
    use SoftDeletes, HasUserStamps, Auditable, HasCode, VisibleBySpv;

    public const CODE_PREFIX = 'CMP';
    public const CODE_PAD = 3;
    protected string $auditModule = 'compliance';
    protected ?string $spvPath = 'land';
    protected $fillable = ['land_id', 'mortgage_created', 'mortgage_date', 'lender_name', 'cersai_reg_no', 'original_docs_custody', 'custodian_name',
        'docs_deposit_date', 'lender_noc_status', 'pdc_open_items', 'target_closure_date', 'escalation_level', 'remarks'];
    protected $casts = ['mortgage_date' => 'date', 'docs_deposit_date' => 'date', 'target_closure_date' => 'date'];

    public function land(): BelongsTo { return $this->belongsTo(Land::class); }

    public function isLate(): bool
    {
        return $this->pdc_open_items > 0 && $this->target_closure_date && $this->target_closure_date->lt(now()->startOfDay());
    }
}
