<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasCode;
use App\Models\Concerns\HasUserStamps;
use App\Models\Concerns\VisibleBySpv;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** RENT_SCHEDULE: one rent instalment of a lease */
class RentSchedule extends Model
{
    use HasUserStamps, Auditable, HasCode, VisibleBySpv;

    public const CODE_PREFIX = 'RS';
    public const CODE_PAD = 4;
    protected string $auditModule = 'schedule';
    protected ?string $spvPath = 'land';
    protected $fillable = ['lease_id', 'land_id', 'due_month', 'due_date', 'rent_amount', 'tds_amount', 'net_payable', 'payment_status', 'alert_sent_flag'];
    protected $casts = ['due_date' => 'date', 'rent_amount' => 'decimal:2', 'tds_amount' => 'decimal:2', 'net_payable' => 'decimal:2'];

    public function lease(): BelongsTo { return $this->belongsTo(Lease::class); }
    public function land(): BelongsTo { return $this->belongsTo(Land::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class, 'schedule_id'); }

    public function totalPaid(): float
    {
        return round((float) ($this->relationLoaded('payments') ? $this->payments->sum('gross_amount') : $this->payments()->sum('gross_amount')), 2);
    }

    public function remaining(): float
    {
        return max(0, round((float) $this->rent_amount - $this->totalPaid(), 2));
    }

    /** Same rules as the template: Paid / Partial Payment / On hold / Overdue / Due soon / Upcoming. */
    public function status(): string
    {
        $paid = $this->totalPaid();
        if ((float) $this->rent_amount > 0 && $paid >= (float) $this->rent_amount - 0.01) {
            return 'Paid';
        }
        if ($this->payment_status === 'Paid' && $paid == 0) {
            return 'Paid';
        }
        if ($paid > 0) {
            return 'Partial Payment';
        }
        if ($this->payment_status === 'On hold') {
            return 'On hold';
        }
        $days = now()->startOfDay()->diffInDays($this->due_date, false);
        if ($days < 0) {
            return 'Overdue';
        }

        return $days <= 30 ? 'Due soon' : 'Upcoming';
    }

    public function refreshStatus(): void
    {
        $st = $this->status();
        $stored = in_array($st, ['Paid', 'Partial Payment'], true) ? $st : ($this->payment_status === 'On hold' ? 'On hold' : 'Pending');
        if ($stored !== $this->payment_status) {
            $this->payment_status = $stored;
            $this->save();
        }
    }
}
