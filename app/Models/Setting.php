<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasUserStamps, Auditable;

    protected string $auditModule = 'setting';

    protected $fillable = ['key', 'value', 'description'];

    public function auditLabel(): string
    {
        return $this->key;
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('settings.all'));
    }

    public static function get(string $key, $default = null)
    {
        $all = Cache::rememberForever('settings.all', fn () => static::pluck('value', 'key')->all());

        return $all[$key] ?? $default;
    }
}
