<?php

namespace App\Services;

use App\Models\Land;
use App\Models\LandOwner;
use App\Models\LandSurvey;
use App\Models\Lease;
use App\Models\Park;
use App\Models\Payment;
use App\Models\RentSchedule;
use App\Models\Spv;
use App\Models\Sublease;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * The 11 reports. Each returns [headings, rows (arrays), indexes of numeric columns to total].
 * Every report respects the user's SPV access and the park / SPV / status / date filters.
 */
class ReportService
{
    public function __construct(private CapacityService $cap, private ShareService $shares) {}

    /** key => [title, description, filters, date label] */
    public function catalogue(): array
    {
        return [
            'park-land' => ['Park-wise land report', 'Parcels, survey numbers, area, leased and subleased acres and MW per park.', ['park', 'date'], 'Parcel created'],
            'survey-land' => ['Survey number-wise land report', 'Every survey / plot number with village, area, farmers, lease and land status.', ['park', 'spv', 'land_status', 'date'], 'Survey created'],
            'farmer-ownership' => ['Farmer-wise ownership report', 'Each farmer’s ownership % in every parcel with the matching share of area.', ['park', 'spv', 'date'], 'Farmer added'],
            'farmer-rent' => ['Farmer-wise rent allocation report', 'Each instalment split per farmer by ownership %, with paid and balance.', ['park', 'spv', 'schedule_status', 'date'], 'Due date'],
            'lease' => ['Lease report', 'All leases with lessor, term, area, MW, rent and escalation terms.', ['park', 'spv', 'lease_status', 'date'], 'Lease start'],
            'sublease' => ['Sublease report', 'All subleases with parent lease, sublessee, acres, MW and period.', ['park', 'spv', 'sublease_status', 'date'], 'Overlapping'],
            'sublessee' => ['Sublessee-wise report', 'Per sublessee: number of subleases, acres, MW and yearly rent.', ['park', 'spv', 'sublease_status', 'date'], 'Overlapping'],
            'spv' => ['SPV-wise report', 'Per SPV: parcels, area, leased and subleased acres and MW, rent due and paid.', ['park', 'date'], 'Rent due'],
            'capacity-allocation' => ['Electricity capacity allocation report', 'How each lease’s MW is allocated to sublessees.', ['park', 'spv', 'sublease_status', 'date'], 'Overlapping'],
            'remaining-capacity' => ['Remaining capacity report', 'Leased, subleased and remaining MW and acres per lease as on a date.', ['park', 'spv', 'asof'], 'As on'],
            'rent-payment' => ['Rent payment report', 'Every rent payment with farmer, instalment, TDS, challan, UTR and SAP voucher.', ['park', 'spv', 'date'], 'Payment date'],
        ];
    }

    public function run(string $key, Request $r): array
    {
        $from = $r->date('from');
        $to = $r->date('to');
        [$h, $rows, $sum] = match ($key) {
            'park-land' => $this->parkLand($r, $from, $to),
            'survey-land' => $this->surveyLand($r, $from, $to),
            'farmer-ownership' => $this->farmerOwnership($r, $from, $to),
            'farmer-rent' => $this->farmerRent($r, $from, $to),
            'lease' => $this->leases($r, $from, $to),
            'sublease' => $this->subleases($r, $from, $to),
            'sublessee' => $this->sublessees($r, $from, $to),
            'spv' => $this->spvs($r, $from, $to),
            'capacity-allocation' => $this->capacityAllocation($r, $from, $to),
            'remaining-capacity' => $this->remainingCapacity($r),
            'rent-payment' => $this->payments($r, $from, $to),
        };
        if ($q = mb_strtolower(trim((string) $r->q))) {
            $rows = $rows->filter(fn ($row) => str_contains(mb_strtolower(implode(' ', array_map('strval', $row))), $q))->values();
        }

        return [$h, $rows->values(), $sum];
    }

    /* ---------------- filters ---------------- */

    private function lands(Request $r)
    {
        return Land::visibleTo()->when($r->park_id, fn ($q, $v) => $v === 'NOP' ? $q->whereNull('park_id') : $q->where('park_id', $v))
            ->when($r->spv_id, fn ($q, $v) => $q->where('spv_id', $v));
    }

    private function onLand(Request $r)
    {
        return fn ($q) => $q->when($r->park_id, fn ($q, $v) => $v === 'NOP' ? $q->whereNull('park_id') : $q->where('park_id', $v))
            ->when($r->spv_id, fn ($q, $v) => $q->where('spv_id', $v));
    }

    private function overlaps($q, string $s, string $e, ?Carbon $from, ?Carbon $to)
    {
        return $q->when($to, fn ($q) => $q->whereDate($s, '<=', $to))->when($from, fn ($q) => $q->whereDate($e, '>=', $from));
    }

    /* ---------------- reports ---------------- */

    private function parkLand(Request $r, $from, $to): array
    {
        $parks = Park::with('state')->when($r->park_id && $r->park_id !== 'NOP', fn ($q) => $q->whereKey($r->park_id))->orderBy('id')->get()->all();
        if (! $r->park_id || $r->park_id === 'NOP') {
            $parks[] = null; // parcels with no park
        }
        $rows = collect($parks)->map(function (?Park $p) use ($from, $to) {
            $lands = Land::visibleTo()->with(['surveys', 'leases'])->when($p, fn ($q) => $q->where('park_id', $p->id), fn ($q) => $q->whereNull('park_id'))
                ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to))->get();
            if (! $p && $lands->isEmpty()) {
                return null;
            }
            $active = $lands->flatMap->leases->where('lease_status', 'Active');
            $sub = $active->sum(fn ($L) => $this->cap->areaUsed($L));
            $subMw = $active->sum(fn ($L) => $this->cap->mwToday($L));

            return [$p?->park_code ?? 'NOP', $p?->park_name ?? 'No park', $p?->state->state_name ?? '—', $p ? (float) $p->total_area_acres : 0, $lands->count(),
                $lands->sum(fn ($l) => $l->surveys->count()), round((float) $lands->sum('area_acres_712'), 2), round((float) $lands->sum('area_under_lease_acres'), 2),
                round((float) $active->sum('leased_area_acres'), 2), round($sub, 2), $p ? (float) $p->installed_capacity_mw : 0, round((float) $active->sum('leased_capacity_mw'), 3), round($subMw, 3)];
        })->filter()->values();

        return [['Park', 'Park name', 'State', 'Park area (ac)', 'Parcels', 'Survey nos.', '7/12 area (ac)', 'Area under lease (ac)', 'Leased (ac)', 'Subleased (ac)', 'Installed MW', 'Leased MW', 'Subleased MW today'],
            $rows, [3, 4, 5, 6, 7, 8, 9, 10, 11, 12]];
    }

    private function surveyLand(Request $r, $from, $to): array
    {
        $rows = LandSurvey::whereHas('land', fn ($q) => $q->visibleTo()->where($this->onLand($r))->when($r->land_status, fn ($q, $v) => $q->where('land_status', $v)))
            ->with(['land.park', 'land.spv', 'land.farmers', 'land.activeLease', 'city', 'district'])
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to))
            ->orderBy('land_id')->orderBy('id')->get()->map(fn ($s) => [
                $s->land->land_ref_no, $s->land->park?->park_code ?? 'NOP', $s->land->spv->spv_code, $s->survey_plot_no, $s->village, $s->city->city_name, $s->district->district_name,
                (float) $s->area_sqm, (float) $s->area_acres, $s->land->farmers->map(fn ($f) => $f->owner_name.' ('.fmt_num($f->ownership_pct).'%)')->implode(', '),
                $s->land->activeLease?->code ?? '—', $s->land->land_status,
            ]);

        return [['Land ref no.', 'Park', 'SPV', 'Survey / plot no.', 'Village', 'City / taluka', 'District', 'Area (sq.m)', 'Area (ac)', 'Farmers', 'Active lease', 'Land status'], $rows, [7, 8]];
    }

    private function farmerOwnership(Request $r, $from, $to): array
    {
        $rows = LandOwner::whereHas('land', fn ($q) => $q->visibleTo()->where($this->onLand($r)))->with(['land.park', 'land.spv'])
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to))
            ->get()->sortBy(fn ($f) => $f->owner_name.' '.$f->land->land_ref_no)->map(fn ($f) => [
                $f->owner_name, $f->code, $f->owner_pan, $f->aadhaar, $f->land->land_ref_no, $f->land->park?->park_code ?? 'NOP', $f->land->spv->spv_code, (float) $f->ownership_pct,
                round((float) $f->land->area_under_lease_acres * (float) $f->ownership_pct / 100, 2), $f->bank_branch, $f->maskedBank(),
            ]);

        return [['Farmer', 'ID', 'PAN', 'Aadhaar', 'Land ref no.', 'Park', 'SPV', 'Ownership %', 'Share of area under lease (ac)', 'Bank & branch', 'Bank A/c'], $rows, [8]];
    }

    private function farmerRent(Request $r, $from, $to): array
    {
        $rows = collect();
        RentSchedule::whereHas('land', fn ($q) => $q->visibleTo()->where($this->onLand($r)))->with(['land.farmers', 'lease', 'payments'])
            ->when($from, fn ($q) => $q->whereDate('due_date', '>=', $from))->when($to, fn ($q) => $q->whereDate('due_date', '<=', $to))
            ->orderBy('due_date')->get()
            ->when($r->status, fn ($c, $v) => $c->filter(fn ($s) => $s->status() === $v))
            ->each(function ($s) use ($rows) {
                foreach ($this->shares->forSchedule($s) as $x) {
                    $rows->push([$x['farmer']->owner_name, $s->land->land_ref_no, $s->lease->code, $s->code, $s->due_date->format('Y-m-d'), $x['pct'], $x['share'], $x['tds'], $x['net'],
                        $x['paid'], $x['remaining'], $s->status()]);
                }
            });

        return [['Farmer', 'Land ref no.', 'Lease', 'Instalment', 'Due date', 'Ownership %', 'Rent share', 'TDS share', 'Net share', 'Paid', 'Balance', 'Instalment status'], $rows, [6, 7, 8, 9, 10]];
    }

    private function leases(Request $r, $from, $to): array
    {
        $rows = Lease::whereHas('land', fn ($q) => $q->visibleTo()->where($this->onLand($r)))->with(['land.park', 'land.spv', 'schedules.payments'])
            ->when($r->status, fn ($q, $v) => $q->where('lease_status', $v))
            ->when($from, fn ($q) => $q->whereDate('lease_start_date', '>=', $from))->when($to, fn ($q) => $q->whereDate('lease_start_date', '<=', $to))
            ->orderBy('id')->get()->map(fn ($L) => [
                $L->code, $L->land->land_ref_no, $L->land->park?->park_code ?? 'NOP', $L->land->spv->spv_code, $L->lessor_name, $L->lessor_pan, (float) $L->leased_area_acres, (float) $L->leased_capacity_mw,
                $L->lease_start_date->format('Y-m-d'), $L->lease_end_date->format('Y-m-d'), (int) $L->lease_duration_mths, (float) $L->rent_rate_per_acre, $L->rent_frequency, (float) $L->rent_amount,
                fmt_num($L->escalation_pct).'% / '.$L->escalation_every_yrs.' yrs', (float) $L->security_deposit, $L->agreement_no, $L->lease_status,
                round($L->schedules->sum('rent_amount'), 2), round($L->schedules->sum(fn ($s) => $s->totalPaid()), 2),
            ]);

        return [['Lease', 'Land ref no.', 'Park', 'SPV', 'Lessor', 'PAN', 'Area (ac)', 'Capacity (MW)', 'Start', 'End', 'Months', 'Rate / acre', 'Frequency', 'Rent / instalment', 'Escalation', 'Security deposit', 'Agreement no.', 'Status', 'Total rent', 'Paid to date'],
            $rows, [6, 7, 15, 18, 19]];
    }

    private function subQuery(Request $r, $from, $to)
    {
        return $this->overlaps(Sublease::whereHas('land', fn ($q) => $q->visibleTo()->where($this->onLand($r))), 'sublease_start', 'sublease_end', $from, $to)
            ->with(['land.park', 'land.spv', 'lease'])->when($r->status, fn ($q, $v) => $q->where('status', $v))->orderBy('id');
    }

    private function subleases(Request $r, $from, $to): array
    {
        $rows = $this->subQuery($r, $from, $to)->get()->map(fn ($s) => [
            $s->code, $s->lease->code, $s->land->land_ref_no, $s->land->park?->park_code ?? 'NOP', $s->land->spv->spv_code, $s->sublessee_name, $s->sublessee_pan,
            (float) $s->sublease_area_acres, (float) $s->capacity_mw, $s->sublease_start->format('Y-m-d'), $s->sublease_end->format('Y-m-d'), (float) $s->sublease_rent,
            $s->agreement_no, $s->lender_noc_obtained, $s->status,
        ]);

        return [['Sublease', 'Lease', 'Land ref no.', 'Park', 'SPV', 'Sublessee', 'PAN', 'Area (ac)', 'Capacity (MW)', 'Start', 'End', 'Rent / yr', 'Agreement no.', 'Lender NOC', 'Status'], $rows, [7, 8, 11]];
    }

    private function sublessees(Request $r, $from, $to): array
    {
        $rows = $this->subQuery($r, $from, $to)->get()->groupBy(fn ($s) => mb_strtolower(trim($s->sublessee_name)))->map(fn ($g) => [
            $g->first()->sublessee_name, $g->first()->sublessee_pan, $g->count(), $g->pluck('land.land_ref_no')->unique()->implode(', '), round((float) $g->sum('sublease_area_acres'), 2),
            round((float) $g->sum('capacity_mw'), 3), round((float) $g->where('status', 'Active')->sum('capacity_mw'), 3), round((float) $g->sum('sublease_rent'), 2),
        ])->sortBy(0)->values();

        return [['Sublessee', 'PAN', 'Subleases', 'Parcels', 'Area (ac)', 'Capacity (MW)', 'Active MW', 'Rent / yr'], $rows, [2, 4, 5, 6, 7]];
    }

    private function spvs(Request $r, $from, $to): array
    {
        $rows = Spv::visibleTo()->with('park')->when($r->park_id, fn ($q, $v) => $q->where('park_id', $v))->orderBy('id')->get()->map(function ($spv) use ($from, $to) {
            $lands = Land::where('spv_id', $spv->id)->get();
            $leases = Lease::whereIn('land_id', $lands->pluck('id'))->where('lease_status', 'Active')->get();
            $sch = RentSchedule::whereIn('land_id', $lands->pluck('id'))->with('payments')
                ->when($from, fn ($q) => $q->whereDate('due_date', '>=', $from))->when($to, fn ($q) => $q->whereDate('due_date', '<=', $to))->get();

            return [$spv->spv_code, $spv->spv_name, $spv->park?->park_code ?? '—', (float) $spv->capacity_mwp, $lands->count(), round((float) $lands->sum('area_under_lease_acres'), 2),
                round((float) $leases->sum('leased_area_acres'), 2), round($leases->sum(fn ($L) => $this->cap->areaUsed($L)), 2), round((float) $leases->sum('leased_capacity_mw'), 3),
                round($leases->sum(fn ($L) => $this->cap->mwToday($L)), 3), round((float) $sch->sum('rent_amount'), 2), round($sch->sum(fn ($s) => $s->totalPaid()), 2),
                round($sch->sum(fn ($s) => $s->remaining()), 2)];
        });

        return [['SPV', 'SPV name', 'Park', 'Capacity (MWp)', 'Parcels', 'Area under lease (ac)', 'Leased (ac)', 'Subleased (ac)', 'Leased MW', 'Subleased MW today', 'Rent due', 'Paid', 'Balance'],
            $rows, [3, 4, 5, 6, 7, 8, 9, 10, 11, 12]];
    }

    private function capacityAllocation(Request $r, $from, $to): array
    {
        $rows = collect();
        Lease::whereHas('land', fn ($q) => $q->visibleTo()->where($this->onLand($r)))->with(['land', 'subleases'])->orderBy('id')->get()->each(function ($L) use ($rows, $r, $from, $to) {
            $subs = $L->subleases->filter(fn ($s) => (! $r->status || $s->status === $r->status)
                && (! $to || $s->sublease_start->lte($to)) && (! $from || $s->sublease_end->gte($from)));
            foreach ($subs as $s) {
                $rows->push([$L->code, $L->land->land_ref_no, (float) $L->leased_capacity_mw, $s->code, $s->sublessee_name, (float) $s->capacity_mw,
                    (float) $L->leased_capacity_mw > 0 ? round((float) $s->capacity_mw / (float) $L->leased_capacity_mw * 100, 2) : 0,
                    (float) $s->sublease_area_acres, $s->sublease_start->format('Y-m-d'), $s->sublease_end->format('Y-m-d'), $s->status]);
            }
        });

        return [['Lease', 'Land ref no.', 'Lease MW', 'Sublease', 'Sublessee', 'Allocated MW', '% of lease MW', 'Area (ac)', 'Start', 'End', 'Status'], $rows, [5, 7]];
    }

    private function remainingCapacity(Request $r): array
    {
        $asof = $r->date('asof') ?? now()->startOfDay();
        $rows = Lease::whereHas('land', fn ($q) => $q->visibleTo()->where($this->onLand($r)))->with(['land.park'])->where('lease_status', 'Active')
            ->whereDate('lease_start_date', '<=', $asof)->whereDate('lease_end_date', '>=', $asof)->orderBy('id')->get()->map(function ($L) use ($asof) {
                $mw = $this->cap->mwPeak($L, $asof->copy(), $asof->copy());
                $ac = $this->cap->areaUsed($L);

                return [$L->code, $L->land->land_ref_no, $L->land->park?->park_code ?? 'NOP', (float) $L->leased_capacity_mw, $mw, round((float) $L->leased_capacity_mw - $mw, 3),
                    (float) $L->leased_area_acres, $ac, round((float) $L->leased_area_acres - $ac, 2), $L->lease_end_date->format('Y-m-d')];
            });

        return [['Lease', 'Land ref no.', 'Park', 'Leased MW', 'Subleased MW', 'Remaining MW', 'Leased (ac)', 'Subleased (ac)', 'Remaining (ac)', 'Lease end'], $rows, [3, 4, 5, 6, 7, 8]];
    }

    private function payments(Request $r, $from, $to): array
    {
        $rows = Payment::whereHas('land', fn ($q) => $q->visibleTo()->where($this->onLand($r)))->with(['land', 'farmer', 'schedule.lease'])
            ->when($from, fn ($q) => $q->whereDate('payment_date', '>=', $from))->when($to, fn ($q) => $q->whereDate('payment_date', '<=', $to))
            ->orderBy('payment_date')->orderBy('id')->get()->map(fn ($p) => [
                $p->code, $p->payment_date->format('Y-m-d'), $p->land->land_ref_no, $p->schedule->lease->code, $p->schedule->code, $p->schedule->due_date->format('Y-m-d'),
                $p->farmer->owner_name, (float) $p->gross_amount, (float) $p->tds_deducted, $p->tds_challan_no, (float) $p->net_paid, $p->utr_reference, $p->sap_voucher_no,
            ]);

        return [['Payment', 'Paid on', 'Land ref no.', 'Lease', 'Instalment', 'Due date', 'Farmer', 'Gross paid', 'TDS', 'TDS challan', 'Net paid', 'UTR', 'SAP voucher'], $rows, [7, 8, 10]];
    }
}
