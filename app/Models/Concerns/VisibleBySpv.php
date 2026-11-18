<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Limits queries to the SPVs a user may see. Super Admin sees everything.
 * A model sets $spvPath = null (own spv_id column), 'self' (the SPV table itself)
 * or a relation path such as 'lease' / 'plot' / 'allocation.schedule.lease'.
 */
trait VisibleBySpv
{
    public function scopeVisibleTo(Builder $query, ?User $user = null): Builder
    {
        $user ??= auth()->user();
        if (! $user || $user->isSuper()) {
            return $query;
        }
        $ids = $user->spvIds();
        $path = property_exists($this, 'spvPath') ? $this->spvPath : null;

        if ($path === 'self') {
            return $query->whereIn($this->getTable().'.id', $ids);
        }
        if ($path === null) {
            return $query->whereIn($this->getTable().'.spv_id', $ids);
        }

        return $query->whereHas($path, fn (Builder $q) => $q->whereIn('spv_id', $ids));
    }
}
