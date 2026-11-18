<?php

namespace App\Services;

use App\Models\Lease;
use App\Models\Park;
use App\Models\Plot;
use App\Models\PlotOwnershipShare;
use App\Models\RentSchedule;
use App\Models\SubLease;
use App\Models\User;

/** Figures for the dashboard tiles and panels, limited to the user's SPVs. */
class DashboardService
{
    public function __construct(private CapacityService $cap) {}

    public function metrics(User $u): array
    {
        $today = now()->toDateString();
        $leases = Lease::visibleTo($u);
        $activeLeases = (clone $leases)->where('status', 'Active');
        $leasedMw = (float) (clone $activeLeases)->sum('leased_capacity_mw');
        $subActive = SubLease::visibleTo($u)->where('status', 'Active')->whereDate('start_date', '<=', $today)->whereDate('end_date', '>=', $today);
        $subMw = (float) (clone $subActive)->sum('capacity_mw');
        $unpaid = RentSchedule::visibleTo($u)->whereIn('status', ['Pending', 'Partially paid'])->whereDate('due_date', '<=', $today)->with('allocations')->get();

        return [
            'parks' => Park::count(),
            'land_acres' => (float) Park::sum('total_land_acres'),
            'survey_acres' => (float) Plot::visibleTo($u)->sum('area_acres'),
            'installed_mw' => (float) Park::sum('installed_capacity_mw'),
            'leased_mw' => $leasedMw,
            'sub_mw' => $subMw,
            'available_mw' => round($leasedMw - $subMw, 3),
            'unleased_mw' => round((float) Park::sum('installed_capacity_mw') - $leasedMw, 3),
            'landowners' => PlotOwnershipShare::whereHas('version', fn ($v) => $v->where('status', 'Finalised')->whereNull('effective_to')
                ->whereHas('plot', fn ($p) => $p->visibleTo($u)))->distinct()->count('party_id'),
            'active_leases' => (clone $activeLeases)->count(),
            'expired_leases' => (clone $leases)->where('status', 'Expired')->count(),
            'active_subleases' => (clone $subActive)->count(),
            'pending_count' => $unpaid->count(),
            'pending_amount' => round($unpaid->sum(fn ($s) => $s->remaining()), 2),
            'plots' => Plot::visibleTo($u)->count(),
        ];
    }

    /** Overdue + due in the next 90 days. */
    public function dueRent(User $u)
    {
        return RentSchedule::visibleTo($u)->with(['lease', 'plot', 'allocations'])->whereIn('status', ['Pending', 'Partially paid'])
            ->whereDate('due_date', '<=', now()->addDays(90))->orderBy('due_date')->limit(15)->get();
    }

    /** Per park: installed, leased (active today), sub-leased today. */
    public function parkCapacity(User $u)
    {
        return Park::orderBy('park_code')->get()->map(function (Park $p) use ($u) {
            $today = now()->toDateString();
            $leased = (float) Lease::visibleTo($u)->where('park_id', $p->id)->where('status', 'Active')->sum('leased_capacity_mw');
            $sub = (float) SubLease::visibleTo($u)->where('park_id', $p->id)->where('status', 'Active')->whereDate('start_date', '<=', $today)->whereDate('end_date', '>=', $today)->sum('capacity_mw');

            return ['park' => $p, 'installed' => (float) $p->installed_capacity_mw, 'leased' => $leased, 'sub' => $sub];
        });
    }

    public function leaseUse(User $u)
    {
        return Lease::visibleTo($u)->with('plot')->where('status', 'Active')->orderByDesc('leased_capacity_mw')->limit(10)->get()
            ->map(fn ($l) => ['lease' => $l, 'used' => $this->cap->subLeasedOn($l), 'total' => (float) $l->leased_capacity_mw]);
    }
}
