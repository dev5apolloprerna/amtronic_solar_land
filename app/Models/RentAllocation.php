<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Per-landowner split of one rent instalment. A snapshot: later ownership changes do not alter it once paid. */
class RentAllocation extends Model
{
    protected $fillable = ['rent_schedule_id', 'party_id', 'share_percent', 'gross_amount', 'tds_amount', 'net_amount', 'paid_amount'];

    protected $casts = ['share_percent' => 'decimal:4', 'gross_amount' => 'decimal:2', 'tds_amount' => 'decimal:2', 'net_amount' => 'decimal:2', 'paid_amount' => 'decimal:2'];

    public function schedule(): BelongsTo { return $this->belongsTo(RentSchedule::class, 'rent_schedule_id'); }
    public function party(): BelongsTo { return $this->belongsTo(Party::class); }
    public function payments(): HasMany { return $this->hasMany(RentPayment::class); }

    public function remaining(): float
    {
        return max(0, round((float) $this->gross_amount - (float) $this->paid_amount, 2));
    }

    public function refreshPaid(): void
    {
        $this->paid_amount = round((float) $this->payments()->sum('gross_amount'), 2);
        $this->save();
    }
}
