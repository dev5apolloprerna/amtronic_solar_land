<?php

namespace App\Models\Concerns;

/** Fills created_by / updated_by from the signed-in user. */
trait HasUserStamps
{
    public static function bootHasUserStamps(): void
    {
        static::creating(function ($model) {
            if ($id = auth()->id()) {
                $model->created_by ??= $id;
                $model->updated_by = $id;
            }
        });
        static::updating(function ($model) {
            if ($id = auth()->id()) {
                $model->updated_by = $id;
            }
        });
    }
}
