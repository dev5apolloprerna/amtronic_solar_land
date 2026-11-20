<?php

namespace App\Services;

use App\Models\Compliance;
use App\Models\Land;
use App\Models\Lease;
use App\Models\Park;
use App\Models\RentSchedule;
use App\Models\User;

/** Dashboard figures (template "Land portfolio") limited to the user's SPVs, plus MW capacity figures. */
class DashboardService
{
    public function __construct(private CapacityService $cap) {}

    public function build(User $u): array
    {
        $lands = Land::visibleTo($u)->get();
        $leases = Lease::visibleTo($u)->with('land')->where('lease_status', 'Active')->orderBy('id')->get();
        $leased = (float) $leases->sum('leased_area_acres');
        $use = $leases->map(fn ($L) => ['lease' => $L, 'used' => $this->cap->areaUsed($L), 'total' => (float) $L->leased_area_acres,
            'mwUsed' => $this->cap->mwToday($L), 'mw' => (float) $L->leased_capacity_mw]);
        $subbed = (float) $use->sum('used');

        $sch = RentSchedule::visibleTo($u)->with(['payments', 'land', 'lease'])->where('payment_status', '!=', 'Paid')->orderBy('due_date')->get();
        $overdue = $sch->filter(fn ($s) => $s->status() === 'Overdue');
        $soon = $sch->filter(fn ($s) => in_array($s->status(), ['Due soon', 'Upcoming'], true) && now()->startOfDay()->diffInDays($s->due_date, false) <= 90);
        $cmp = Compliance::visibleTo($u)->get();

        $leasedMw = (float) $leases->sum('leased_capacity_mw');
        $subMw = (float) $use->sum('mwUsed');
        $installed = (float) Park::sum('installed_capacity_mw');

        return [
            'parcels' => $lands->count(),
            'identified' => (float) $lands->sum('area_under_lease_acres'),
            'leased' => $leased,
            'activeLeases' => $leases->count(),
            'subbed' => $subbed,
            'overdue' => $overdue,
            'openPdc' => (int) $cmp->sum('pdc_open_items'),
            'pdcParcels' => $cmp->where('pdc_open_items', '>', 0)->count(),
            'due' => $overdue->concat($soon)->sortBy('due_date')->values(),
            'use' => $use,
            'mw' => ['installed' => $installed, 'leased' => $leasedMw, 'sub' => $subMw, 'free' => round($leasedMw - $subMw, 3), 'unleased' => round($installed - $leasedMw, 3)],
            'parks' => Park::orderBy('id')->get()->map(fn ($p) => ['park' => $p, 'installed' => (float) $p->installed_capacity_mw, 'leased' => $this->cap->parkLeasedToday($p)]),
        ];
    }
}
