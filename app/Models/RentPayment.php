<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUserStamps;
use App\Models\Concerns\VisibleBySpv;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentPayment extends Model
{
    use SoftDeletes, HasUserStamps, Auditable, VisibleBySpv;

    protected string $auditModule = 'payment';
    protected ?string $spvPath = 'allocation.schedule.lease';

    protected $fillable = ['payment_no', 'rent_allocation_id', 'payment_date', 'gross_amount', 'tds_deducted', 'tds_challan_no', 'net_paid', 'payment_mode',
        'utr_reference', 'sap_voucher_no', 'remarks'];

    protected $casts = ['payment_date' => 'date', 'gross_amount' => 'decimal:2', 'tds_deducted' => 'decimal:2', 'net_paid' => 'decimal:2'];

    public function auditLabel(): string
    {
        return (string) $this->payment_no;
    }

    public function allocation(): BelongsTo { return $this->belongsTo(RentAllocation::class, 'rent_allocation_id'); }

    public static function nextNumber(): string
    {
        $last = static::withTrashed()->max('id') ?? 0;

        return 'PAY'.str_pad((string) ($last + 1), 6, '0', STR_PAD_LEFT);
    }
}
