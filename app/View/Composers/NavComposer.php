<?php

namespace App\View\Composers;

use App\Models\AuditLog;
use App\Models\City;
use App\Models\District;
use App\Models\Land;
use App\Models\Park;
use App\Models\Spv;
use App\Models\State;
use App\Models\User;
use Illuminate\View\View;

/** Row counts shown beside each item of the left menu (as in the template). */
class NavComposer
{
    public function compose(View $view): void
    {
        if (! auth()->check()) {
            return;
        }
        $view->with('navCounts', [
            'land' => Land::visibleTo()->count(),
            'state' => State::count(),
            'district' => District::count(),
            'city' => City::count(),
            'park' => Park::count(),
            'spv' => Spv::visibleTo()->count(),
            'report' => 11,
            'user' => User::count(),
            'audit' => AuditLog::count(),
        ]);
    }
}
