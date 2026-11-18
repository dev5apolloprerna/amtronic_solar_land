<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandRefSequence extends Model
{
    protected $fillable = ['scope_key', 'last_number'];
}
