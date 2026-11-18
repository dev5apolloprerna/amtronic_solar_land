<?php

namespace App\View\Composers;

use App\Models\ComplianceRecord;
use App\Models\Document;
use App\Models\Lease;
use App\Models\Park;
use App\Models\Party;
use App\Models\Plot;
use App\Models\RentPayment;
use App\Models\RentSchedule;
use App\Models\Spv;
use App\Models\State;
use App\Models\City;
use App\Models\SubLease;
use App\Models\Taluka;
use Illuminate\View\View;

/** Record counts beside each nav item, limited to the user's SPVs. */
class NavComposer
{
    public function compose(View $view): void
    {
        $u = auth()->user();
        if (! $u) {
            return;
        }
        $view->with('navCounts', [
            'plot' => Plot::visibleTo($u)->count(),
            'lease' => Lease::visibleTo($u)->count(),
            'schedule' => RentSchedule::visibleTo($u)->whereIn('status', ['Pending', 'Partially paid'])->whereDate('due_date', '<', now())->count() ?: '',
            'payment' => RentPayment::visibleTo($u)->count(),
            'sublease' => SubLease::visibleTo($u)->count(),
            'compliance' => ComplianceRecord::visibleTo($u)->count(),
            'document' => Document::visibleTo($u)->count(),
            'party' => Party::count(),
            'park' => Park::count(),
            'spv' => Spv::visibleTo($u)->count(),
            'state' => State::count(),
            'city' => City::count(),
            'taluka' => Taluka::count(),
        ]);
    }
}
