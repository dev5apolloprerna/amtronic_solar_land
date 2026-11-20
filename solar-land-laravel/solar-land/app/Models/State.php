<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/** STATE_MASTER */
class State extends Model
{
    use SoftDeletes, HasUserStamps, Auditable;

    public const CODE_PREFIX = 'ST';
    protected string $auditModule = 'state';
    protected $fillable = ['state_name', 'state_code'];

    public function auditLabel(): string { return $this->state_name; }

    /** [id => 'Gujarat (GJ)'] for dropdowns */
    public static function stateOptions(): array
    {
        return static::orderBy('id')->get()->mapWithKeys(fn ($s) => [$s->id => "$s->state_name ($s->state_code)"])->all();
    }
    public function districts(): HasMany { return $this->hasMany(District::class); }
    public function lands(): HasMany { return $this->hasMany(Land::class); }
}
