<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Party extends Model
{
    use SoftDeletes, HasUserStamps, Auditable;

    protected string $auditModule = 'party';

    protected $fillable = ['party_code', 'party_name', 'party_type', 'contact_person', 'mobile', 'email', 'address', 'state_id', 'city_id', 'taluka_id',
        'pincode', 'pan', 'gst_no', 'aadhaar_last4', 'voter_id', 'bank_account_no', 'bank_ifsc', 'bank_name_branch', 'status'];

    protected $casts = ['bank_account_no' => 'encrypted'];

    public function auditLabel(): string
    {
        return $this->party_code;
    }

    public function state(): BelongsTo { return $this->belongsTo(State::class); }
    public function city(): BelongsTo { return $this->belongsTo(City::class); }
    public function taluka(): BelongsTo { return $this->belongsTo(Taluka::class); }

    public function ownershipShares(): HasMany { return $this->hasMany(PlotOwnershipShare::class); }
    public function rentAllocations(): HasMany { return $this->hasMany(RentAllocation::class); }
    public function subLeases(): HasMany { return $this->hasMany(SubLease::class); }
    public function spvs(): HasMany { return $this->hasMany(Spv::class); }

    public function maskedBankAccount(): string
    {
        $ac = (string) $this->bank_account_no;

        return $ac === '' ? '—' : str_repeat('X', max(0, strlen($ac) - 4)).substr($ac, -4);
    }

    public static function nextCode(): string
    {
        $last = static::withTrashed()->max('id') ?? 0;

        return 'PTY'.str_pad((string) ($last + 1), 5, '0', STR_PAD_LEFT);
    }
}
