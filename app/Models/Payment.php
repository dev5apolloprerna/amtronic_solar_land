<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasCode;
use App\Models\Concerns\HasUserStamps;
use App\Models\Concerns\VisibleBySpv;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/** PAYMENT_REGISTER: farmer-wise rent payment against an instalment */
class Payment extends Model
{
    use SoftDeletes, HasUserStamps, Auditable, HasCode, VisibleBySpv;

    public const CODE_PREFIX = 'PAY';
    public const CODE_PAD = 3;
    protected string $auditModule = 'payment';
    protected ?string $spvPath = 'land';
    protected $fillable = ['schedule_id', 'land_id', 'farmer_id', 'payment_date', 'gross_amount', 'tds_deducted', 'tds_challan_no', 'net_paid', 'utr_reference', 'sap_voucher_no'];
    protected $casts = ['payment_date' => 'date', 'gross_amount' => 'decimal:2', 'tds_deducted' => 'decimal:2', 'net_paid' => 'decimal:2'];

    public function schedule(): BelongsTo { return $this->belongsTo(RentSchedule::class, 'schedule_id'); }
    public function land(): BelongsTo { return $this->belongsTo(Land::class); }
    public function farmer(): BelongsTo { return $this->belongsTo(LandOwner::class, 'farmer_id')->withTrashed(); }
}
