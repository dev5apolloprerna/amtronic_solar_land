<?php

namespace App\Services;

use App\Models\Lease;
use App\Models\Park;
use App\Models\Party;
use App\Models\Plot;
use App\Models\PlotOwnershipShare;
use App\Models\RentAllocation;
use App\Models\RentPayment;
use App\Models\Spv;
use App\Models\SubLease;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * The 11 reports. Each one returns rows as plain arrays so the same data feeds
 * the screen (search + pagination), Excel and PDF.
 * 'sum' lists the column indexes totalled in the footer.
 */
class ReportService
{
    public function __construct(private CapacityService $cap) {}

    public function catalogue(): array
    {
        return [
            'park-land' => ['Park-wise land report', 'Land area, survey numbers, leased area and MW per park.', ['park', 'date'], 'Survey created'],
            'survey-land' => ['Survey number-wise land report', 'Every survey number with area, owners, lease and status.', ['park', 'spv', 'land_status', 'date'], 'Survey created'],
            'owner-ownership' => ['Landowner-wise ownership report', 'Each landowner’s share in every survey number, current and historical.', ['park', 'party', 'ownership_state', 'date'], 'Effective from'],
            'owner-rent' => ['Landowner-wise rent allocation report', 'Rent split per landowner per instalment with paid and balance.', ['park', 'party', 'paid_state', 'date'], 'Due date'],
            'lease' => ['Lease report', 'All leases with term, area, MW and rent terms.', ['park', 'spv', 'lease_status', 'date'], 'Lease start'],
            'sublease' => ['Sub-lease report', 'All sub-leases with parent lease, party, MW and period.', ['park', 'party', 'sublease_status', 'date'], 'Overlapping'],
            'party' => ['Party-wise report', 'Per party: survey shares, sub-leased MW, rent allocated, paid and balance.', ['party_type', 'date'], 'Rent due'],
            'spv' => ['SPV-wise report', 'Per SPV: survey numbers, area, leased and sub-leased MW, rent due and paid.', ['park', 'date'], 'Rent due'],
            'capacity-allocation' => ['Electricity capacity allocation report', 'How each lease’s MW is allocated to sub-lessees.', ['park', 'sublease_status', 'date'], 'Overlapping'],
            'remaining-capacity' => ['Remaining capacity report', 'Installed, leased, sub-leased and remaining MW per lease as on a date.', ['park', 'asof'], 'As on'],
            'rent-payment' => ['Rent payment report', 'Every rent payment with landowner, instalment, TDS and UTR.', ['park', 'party', 'date'], 'Payment date'],
        ];
    }

    /** @return array{0: array, 1: Collection, 2: array} [headings, rows, sum column indexes] */
    public function run(string $key, Request $r): array
    {
        [$from, $to] = [$r->date('from'), $r->date('to')];
        [$h, $rows, $sum] = match ($key) {
            'park-land' => $this->parkLand($r, $from, $to),
            'survey-land' => $this->surveyLand($r, $from, $to),
            'owner-ownership' => $this->ownerOwnership($r, $from, $to),
            'owner-rent' => $this->ownerRent($r, $from, $to),
            'lease' => $this->leases($r, $from, $to),
            'sublease' => $this->subleases($r, $from, $to),
            'party' => $this->parties($r, $from, $to),
            'spv' => $this->spvs($r, $from, $to),
            'capacity-allocation' => $this->capacityAllocation($r, $from, $to),
            'remaining-capacity' => $this->remainingCapacity($r),
            'rent-payment' => $this->payments($r, $from, $to),
        };
        if ($q = mb_strtolower(trim((string) $r->q))) {
            $rows = $rows->filter(fn ($row) => str_contains(mb_strtolower(implode(' ', array_map('strval', $row))), $q))->values();
        }

        return [$h, $rows, $sum];
    }

    /* ---------------- reports ---------------- */

    private function parkLand(Request $r, $from, $to): array
    {
        $rows = Park::with(['state', 'city', 'taluka'])->when($r->park_id, fn ($q, $v) => $q->whereKey($v))->orderBy('park_code')->get()->map(function (Park $p) use ($from, $to) {
            $plots = Plot::visibleTo()->where('park_id', $p->id)->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to));
            $leases = Lease::visibleTo()->where('park_id', $p->id)->where('status', 'Active');
            $leasedMw = (float) (clone $leases)->sum('leased_capacity_mw');
            $subMw = (float) SubLease::visibleTo()->where('park_id', $p->id)->where('status', 'Active')->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now())->sum('capacity_mw');

            return [$p->park_code, $p->park_name, $p->taluka->name.', '.$p->city->name.', '.$p->state->name, (float) $p->total_land_acres, (clone $plots)->count(),
                round((float) (clone $plots)->sum('area_acres'), 4), round((float) (clone $plots)->sum('area_under_lease_acres'), 4), round((float) (clone $leases)->sum('lease_area_acres'), 4),
                (float) $p->installed_capacity_mw, $leasedMw, $subMw, round($leasedMw - $subMw, 3), round((float) $p->installed_capacity_mw - $leasedMw, 3)];
        });

        return [['Park code', 'Park', 'Location', 'Park land (ac)', 'Survey nos.', 'Survey area (ac)', 'Under lease (ac)', 'Leased area, active (ac)', 'Installed MW', 'Leased MW', 'Sub-leased MW today', 'Available on leases MW', 'Unleased park MW'],
            $rows, [3, 4, 5, 6, 7, 8, 9, 10, 11, 12]];
    }

    private function surveyLand(Request $r, $from, $to): array
    {
        $rows = Plot::visibleTo()->with(['park', 'spv', 'taluka', 'city', 'state', 'currentOwnership.shares.party', 'activeLease'])
            ->when($r->park_id, fn ($q, $v) => $q->where('park_id', $v))->when($r->spv_id, fn ($q, $v) => $q->where('spv_id', $v))
            ->when($r->status, fn ($q, $v) => $q->where('land_status', $v))
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to))
            ->orderBy('land_ref_no')->get()->map(fn (Plot $p) => [
                $p->land_ref_no, $p->park->park_code, $p->spv->spv_code, $p->village, $p->surveyLabel(), $p->taluka->name, (float) $p->total_area, $p->land_unit,
                (float) $p->area_acres, (float) $p->area_under_lease_acres, $p->currentOwnership?->shares->count() ?? 0, $p->currentOwnership ? (float) $p->currentOwnership->total_percent : 0,
                $p->activeLease?->lease_no ?? '—', $p->activeLease ? (float) $p->activeLease->leased_capacity_mw : 0, $p->land_status, $p->created_at->format('Y-m-d'),
            ]);

        return [['Land ref no.', 'Park', 'SPV', 'Village', 'Survey no.', 'Taluka', 'Area', 'Unit', 'Area (ac)', 'Under lease (ac)', 'Owners', 'Ownership %', 'Active lease', 'Lease MW', 'Status', 'Created'],
            $rows, [8, 9, 13]];
    }

    private function ownerOwnership(Request $r, $from, $to): array
    {
        $rows = PlotOwnershipShare::with(['party', 'version.plot.park'])
            ->whereHas('version', fn ($v) => $v->whereIn('status', $r->status === 'History' ? ['Superseded'] : ($r->status === 'Current' ? ['Finalised'] : ['Finalised', 'Superseded']))
                ->when($from, fn ($q) => $q->whereDate('effective_from', '>=', $from))->when($to, fn ($q) => $q->whereDate('effective_from', '<=', $to))
                ->whereHas('plot', fn ($p) => $p->visibleTo()->when($r->park_id, fn ($q, $v) => $q->where('park_id', $v))))
            ->when($r->party_id, fn ($q, $v) => $q->where('party_id', $v))
            ->get()->sortBy(fn ($s) => $s->party->party_name.' '.$s->version->plot->land_ref_no)->map(fn ($s) => [
                $s->party->party_name, $s->party->party_code, $s->party->pan, $s->version->plot->land_ref_no, $s->version->plot->park->park_code, $s->version->plot->village,
                $s->version->plot->surveyLabel(), (float) $s->share_percent, round((float) $s->version->plot->area_acres * (float) $s->share_percent / 100, 4),
                'v'.$s->version->version_no, $s->version->effective_from?->format('Y-m-d'), $s->version->effective_to?->format('Y-m-d') ?? 'Current', $s->version->status,
            ])->values();

        return [['Landowner', 'Party code', 'PAN', 'Land ref no.', 'Park', 'Village', 'Survey no.', 'Share %', 'Share of area (ac)', 'Version', 'Effective from', 'Effective to', 'Status'], $rows, [8]];
    }

    private function ownerRent(Request $r, $from, $to): array
    {
        $rows = RentAllocation::with(['party', 'schedule.lease', 'schedule.plot'])
            ->whereHas('schedule', fn ($s) => $s->visibleTo()->when($from, fn ($q) => $q->whereDate('due_date', '>=', $from))->when($to, fn ($q) => $q->whereDate('due_date', '<=', $to))
                ->when($r->park_id, fn ($q, $v) => $q->whereHas('lease', fn ($l) => $l->where('park_id', $v))))
            ->when($r->party_id, fn ($q, $v) => $q->where('party_id', $v))
            ->when($r->status === 'Unpaid', fn ($q) => $q->whereColumn('paid_amount', '<', 'gross_amount'))
            ->when($r->status === 'Paid', fn ($q) => $q->whereColumn('paid_amount', '>=', 'gross_amount'))
            ->get()->sortBy(fn ($a) => $a->party->party_name.$a->schedule->due_date->format('Ymd'))->map(fn ($a) => [
                $a->party->party_name, $a->party->pan, $a->schedule->plot->land_ref_no, $a->schedule->lease->lease_no, $a->schedule->installment_no, $a->schedule->due_date->format('Y-m-d'),
                (float) $a->share_percent, (float) $a->gross_amount, (float) $a->tds_amount, (float) $a->net_amount, (float) $a->paid_amount, $a->remaining(),
            ])->values();

        return [['Landowner', 'PAN', 'Land ref no.', 'Lease', 'Instalment', 'Due date', 'Share %', 'Gross', 'TDS', 'Net', 'Paid', 'Balance'], $rows, [7, 8, 9, 10, 11]];
    }

    private function leases(Request $r, $from, $to): array
    {
        $rows = Lease::visibleTo()->with(['plot.currentOwnership.shares.party', 'park', 'spv'])
            ->when($r->park_id, fn ($q, $v) => $q->where('park_id', $v))->when($r->spv_id, fn ($q, $v) => $q->where('spv_id', $v))->when($r->status, fn ($q, $v) => $q->where('status', $v))
            ->when($from, fn ($q) => $q->whereDate('lease_start_date', '>=', $from))->when($to, fn ($q) => $q->whereDate('lease_start_date', '<=', $to))
            ->orderBy('lease_no')->get()->map(fn (Lease $l) => [
                $l->lease_no, $l->lease_date?->format('Y-m-d'), $l->plot->land_ref_no, $l->park->park_code, $l->spv->spv_code,
                $l->plot->currentOwnership?->shares->map(fn ($s) => $s->party->party_name)->implode(', '),
                $l->lease_start_date->format('Y-m-d'), $l->lease_end_date->format('Y-m-d'), $l->lease_duration_months, (float) $l->lease_area_acres, (float) $l->leased_capacity_mw,
                $this->cap->subLeasedOn($l), $l->rent_type, (float) $l->rent_rate, (float) $l->annual_rent, $l->payment_frequency, (float) $l->security_deposit, $l->status,
            ]);

        return [['Lease no.', 'Lease date', 'Land ref no.', 'Park', 'SPV', 'Lessors', 'Start', 'End', 'Months', 'Area (ac)', 'Leased MW', 'Sub-leased MW today', 'Rent type', 'Rate', 'Annual rent', 'Frequency', 'Security deposit', 'Status'],
            $rows, [9, 10, 11, 14, 16]];
    }

    private function subleases(Request $r, $from, $to): array
    {
        $rows = $this->subQuery($r, $from, $to)->get()->map(fn (SubLease $s) => [
            $s->sub_lease_no, $s->lease->lease_no, $s->lease->plot->land_ref_no, $s->park->park_code, $s->spv->spv_code, $s->party->party_name, (float) $s->capacity_mw,
            $s->start_date->format('Y-m-d'), $s->end_date->format('Y-m-d'), (float) $s->rent_amount, $s->rent_frequency, $s->payment_terms, $s->agreement_no, $s->lender_noc_obtained, $s->status,
        ]);

        return [['Sub-lease no.', 'Parent lease', 'Land ref no.', 'Park', 'SPV', 'Sub-lessee', 'MW', 'Start', 'End', 'Rent', 'Frequency', 'Payment terms', 'Agreement no.', 'Lender NOC', 'Status'], $rows, [6, 9]];
    }

    private function parties(Request $r, $from, $to): array
    {
        $rows = Party::when($r->party_type, fn ($q, $v) => $q->where('party_type', $v))->orderBy('party_name')->get()->map(function (Party $p) use ($from, $to) {
            $alloc = RentAllocation::where('party_id', $p->id)->whereHas('schedule', fn ($s) => $s->visibleTo()
                ->when($from, fn ($q) => $q->whereDate('due_date', '>=', $from))->when($to, fn ($q) => $q->whereDate('due_date', '<=', $to)));
            $gross = (float) (clone $alloc)->sum('gross_amount');
            $paid = (float) (clone $alloc)->sum('paid_amount');
            $shares = PlotOwnershipShare::where('party_id', $p->id)->whereHas('version', fn ($v) => $v->where('status', 'Finalised')->whereNull('effective_to'))->count();
            $subs = SubLease::visibleTo()->where('party_id', $p->id)->where('status', 'Active');

            return [$p->party_code, $p->party_name, $p->party_type, $p->pan, $p->mobile, $shares, (clone $subs)->count(), (float) (clone $subs)->sum('capacity_mw'), $gross, $paid, round($gross - $paid, 2), $p->status];
        });

        return [['Code', 'Party', 'Type', 'PAN', 'Mobile', 'Survey shares (current)', 'Active sub-leases', 'Sub-leased MW', 'Rent allocated', 'Rent paid', 'Balance', 'Status'], $rows, [5, 6, 7, 8, 9, 10]];
    }

    private function spvs(Request $r, $from, $to): array
    {
        $rows = Spv::visibleTo()->with(['park', 'party'])->when($r->park_id, fn ($q, $v) => $q->where('park_id', $v))->orderBy('spv_code')->get()->map(function (Spv $s) use ($from, $to) {
            $plots = Plot::where('spv_id', $s->id);
            $leases = Lease::where('spv_id', $s->id)->where('status', 'Active');
            $leasedMw = (float) (clone $leases)->sum('leased_capacity_mw');
            $subMw = (float) SubLease::where('spv_id', $s->id)->where('status', 'Active')->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now())->sum('capacity_mw');
            $alloc = RentAllocation::whereHas('schedule.lease', fn ($l) => $l->where('spv_id', $s->id))
                ->whereHas('schedule', fn ($q) => $q->when($from, fn ($q) => $q->whereDate('due_date', '>=', $from))->when($to, fn ($q) => $q->whereDate('due_date', '<=', $to)));

            return [$s->spv_code, $s->spv_name, $s->park->park_code, $s->party?->party_name, (float) $s->capacity_mwp, (clone $plots)->count(), round((float) (clone $plots)->sum('area_acres'), 4),
                (clone $leases)->count(), $leasedMw, $subMw, round($leasedMw - $subMw, 3), (float) (clone $alloc)->sum('gross_amount'), (float) (clone $alloc)->sum('paid_amount'), $s->project_status];
        });

        return [['SPV', 'Name', 'Park', 'Owning company', 'Capacity MWp', 'Survey nos.', 'Area (ac)', 'Active leases', 'Leased MW', 'Sub-leased MW today', 'Available MW', 'Rent due', 'Rent paid', 'Project status'],
            $rows, [4, 5, 6, 7, 8, 9, 10, 11, 12]];
    }

    private function capacityAllocation(Request $r, $from, $to): array
    {
        $rows = collect();
        Lease::visibleTo()->with(['plot', 'park'])->where('status', 'Active')->when($r->park_id, fn ($q, $v) => $q->where('park_id', $v))->orderBy('lease_no')->get()
            ->each(function (Lease $l) use ($rows, $r, $from, $to) {
                $subs = SubLease::with('party')->where('lease_id', $l->id)->when($r->status, fn ($q, $v) => $q->where('status', $v))
                    ->when($from, fn ($q) => $q->whereDate('end_date', '>=', $from))->when($to, fn ($q) => $q->whereDate('start_date', '<=', $to))->orderBy('start_date')->get();
                $base = [$l->park->park_code, $l->lease_no, $l->plot->land_ref_no, (float) $l->leased_capacity_mw];
                if ($subs->isEmpty()) {
                    $rows->push(array_merge($base, ['— not sub-leased —', 0, '', '', '', 0]));
                }
                foreach ($subs as $s) {
                    $rows->push(array_merge($base, [$s->party->party_name.' ('.$s->sub_lease_no.')', (float) $s->capacity_mw, $s->start_date->format('Y-m-d'), $s->end_date->format('Y-m-d'), $s->status,
                        (float) $l->leased_capacity_mw > 0 ? round((float) $s->capacity_mw / (float) $l->leased_capacity_mw * 100, 2) : 0]));
                }
            });

        return [['Park', 'Lease', 'Land ref no.', 'Lease MW', 'Allocated to', 'Allocated MW', 'From', 'To', 'Status', '% of lease'], $rows, [5]];
    }

    private function remainingCapacity(Request $r): array
    {
        $asOf = $r->date('to') ? Carbon::parse($r->date('to')) : now()->startOfDay();
        $rows = Lease::visibleTo()->with(['plot', 'park', 'spv'])->where('status', 'Active')->whereDate('lease_start_date', '<=', $asOf)->whereDate('lease_end_date', '>=', $asOf)
            ->when($r->park_id, fn ($q, $v) => $q->where('park_id', $v))->orderBy('park_id')->orderBy('lease_no')->get()
            ->map(function (Lease $l) use ($asOf) {
                $sub = $this->cap->subLeasedOn($l, $asOf);

                return [$l->park->park_code, (float) $l->park->installed_capacity_mw, $l->spv->spv_code, $l->lease_no, $l->plot->land_ref_no, (float) $l->leased_capacity_mw, $sub,
                    round((float) $l->leased_capacity_mw - $sub, 3), (float) $l->leased_capacity_mw > 0 ? round($sub / (float) $l->leased_capacity_mw * 100, 1) : 0, $asOf->format('Y-m-d')];
            });

        return [['Park', 'Park installed MW', 'SPV', 'Lease', 'Land ref no.', 'Lease MW', 'Sub-leased MW', 'Remaining MW', 'Used %', 'As on'], $rows, [5, 6, 7]];
    }

    private function payments(Request $r, $from, $to): array
    {
        $rows = RentPayment::visibleTo()->with(['allocation.party', 'allocation.schedule.lease.park', 'allocation.schedule.plot'])
            ->when($r->park_id, fn ($q, $v) => $q->whereHas('allocation.schedule.lease', fn ($l) => $l->where('park_id', $v)))
            ->when($r->party_id, fn ($q, $v) => $q->whereHas('allocation', fn ($a) => $a->where('party_id', $v)))
            ->when($from, fn ($q) => $q->whereDate('payment_date', '>=', $from))->when($to, fn ($q) => $q->whereDate('payment_date', '<=', $to))
            ->orderBy('payment_date')->get()->map(fn ($p) => [
                $p->payment_no, $p->payment_date->format('Y-m-d'), $p->allocation->party->party_name, $p->allocation->party->pan, $p->allocation->schedule->plot->land_ref_no,
                $p->allocation->schedule->lease->park->park_code, $p->allocation->schedule->lease->lease_no, $p->allocation->schedule->due_date->format('Y-m-d'),
                (float) $p->gross_amount, (float) $p->tds_deducted, $p->tds_challan_no, (float) $p->net_paid, $p->payment_mode, $p->utr_reference, $p->sap_voucher_no,
            ]);

        return [['Payment no.', 'Date', 'Landowner', 'PAN', 'Land ref no.', 'Park', 'Lease', 'Instalment due', 'Gross', 'TDS', 'TDS challan', 'Net paid', 'Mode', 'UTR', 'SAP voucher'], $rows, [8, 9, 11]];
    }

    private function subQuery(Request $r, $from, $to)
    {
        return SubLease::visibleTo()->with(['lease.plot', 'party', 'park', 'spv'])
            ->when($r->park_id, fn ($q, $v) => $q->where('park_id', $v))->when($r->party_id, fn ($q, $v) => $q->where('party_id', $v))
            ->when($r->status, fn ($q, $v) => $q->where('status', $v))
            ->when($from, fn ($q) => $q->whereDate('end_date', '>=', $from))->when($to, fn ($q) => $q->whereDate('start_date', '<=', $to))
            ->orderBy('sub_lease_no');
    }
}
