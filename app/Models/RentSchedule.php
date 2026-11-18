<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUserStamps;
use App\Models\Concerns\VisibleBySpv;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RentSchedule extends Model
{
    use HasUserStamps, Auditable, VisibleBySpv;

    protected string $auditModule = 'schedule';
    protected ?string $spvPath = 'lease';

    protected $fillable = ['lease_id', 'plot_id', 'installment_no', 'period_from', 'period_to', 'due_date', 'rent_amount', 'tds_amount', 'net_payable',
        'ownership_version_id', 'status', 'alert_sent'];

    protected $casts = ['period_from' => 'date', 'period_to' => 'date', 'due_date' => 'date', 'rent_amount' => 'decimal:2', 'tds_amount' => 'decimal:2',
        'net_payable' => 'decimal:2'];

    public function auditLabel(): string
    {
        return 'RS'.str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    public function lease(): BelongsTo { return $this->belongsTo(Lease::class); }
    public function plot(): BelongsTo { return $this->belongsTo(Plot::class); }
    public function ownershipVersion(): BelongsTo { return $this->belongsTo(PlotOwnershipVersion::class, 'ownership_version_id'); }
    public function allocations(): HasMany { return $this->hasMany(RentAllocation::class); }

    public function paidTotal(): float
    {
        return (float) $this->allocations->sum('paid_amount');
    }

    public function remaining(): float
    {
        return max(0, round((float) $this->rent_amount - $this->paidTotal(), 2));
    }

    /** Status shown on screen: stored status plus Overdue / Due soon / Upcoming for unpaid rows. */
    public function displayStatus(): string
    {
        if (in_array($this->status, ['Paid', 'Partially paid', 'On hold'], true)) {
            return $this->status;
        }
        if (! $this->ownership_version_id) {
            return 'Ownership not finalised';
        }
        $days = now()->startOfDay()->diffInDays($this->due_date, false);
        if ($days < 0) {
            return 'Overdue';
        }

        return $days <= 30 ? 'Due soon' : 'Upcoming';
    }

    /** Recompute stored status from allocations. */
    public function refreshStatus(): void
    {
        $this->loadMissing('allocations');
        $paid = $this->paidTotal();
        if ($this->status === 'On hold' && $paid <= 0) {
            return;
        }
        $this->status = $paid <= 0 ? 'Pending' : ($paid >= (float) $this->rent_amount - 0.01 ? 'Paid' : 'Partially paid');
        $this->save();
    }
}
