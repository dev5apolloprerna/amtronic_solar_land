<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\Park;
use App\Models\RentSchedule;
use App\Services\RentAllocationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Instalment-level view of rent: totals, paid, remaining. Rows are generated from the lease, never added by hand. */
class RentScheduleController extends Controller
{
    public const FILTER_STATUSES = ['Overdue', 'Due soon', 'Upcoming', 'Partially paid', 'Paid', 'On hold', 'Ownership not finalised'];

    public function index(Request $request)
    {
        $q = $this->query($request)->with(['lease', 'plot', 'allocations'])->orderBy('due_date');

        if ($export = $this->maybeExport('schedule', 'Rent Schedule', ['Instalment', 'Lease', 'Land ref no.', 'No.', 'Period from', 'Period to', 'Due date', 'Rent', 'TDS', 'Net payable', 'Paid', 'Remaining', 'Status'],
            $q->get()->map(fn ($s) => [$s->auditLabel(), $s->lease->lease_no, $s->plot->land_ref_no, $s->installment_no, $s->period_from->format('Y-m-d'), $s->period_to->format('Y-m-d'),
                $s->due_date->format('Y-m-d'), (float) $s->rent_amount, (float) $s->tds_amount, (float) $s->net_payable, $s->paidTotal(), $s->remaining(), $s->displayStatus()]))) {
            return $export;
        }

        return view('schedules.index', [
            'rows' => $q->paginate(50)->withQueryString(),
            'total' => RentSchedule::visibleTo()->count(),
            'leases' => Lease::visibleTo()->orderBy('lease_no')->get()->mapWithKeys(fn ($l) => [$l->id => $l->lease_no]),
            'parks' => Park::orderBy('park_code')->pluck('park_code', 'id'),
        ]);
    }

    public function edit(RentSchedule $schedule)
    {
        $this->authorizeSchedule($schedule);
        $ro = ! $this->canEdit('schedule') || in_array($schedule->status, ['Paid', 'Partially paid'], true);

        return $this->drawer(($ro ? 'View' : 'Edit').' instalment', 'RENT_SCHEDULES · '.$schedule->auditLabel().' · '.$schedule->lease->lease_no, 'schedules.form', ['s' => $schedule], $ro, 'schedule');
    }

    public function update(Request $request, RentSchedule $schedule, RentAllocationService $allocations)
    {
        $this->authorizeSchedule($schedule);
        if (in_array($schedule->status, ['Paid', 'Partially paid'], true)) {
            return $this->refuse('This instalment already has payments. Its amount and due date can no longer change.');
        }
        $data = $request->validate([
            'due_date' => ['required', 'date', 'after_or_equal:'.$schedule->lease->lease_start_date->format('Y-m-d'), 'before_or_equal:'.$schedule->lease->lease_end_date->format('Y-m-d')],
            'rent_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['Pending', 'On hold'])],
            'alert_sent' => ['required', Rule::in(['Yes', 'No'])],
        ], ['due_date.after_or_equal' => 'Due date must fall within the lease term.', 'due_date.before_or_equal' => 'Due date must fall within the lease term.']);
        $lease = $schedule->lease;
        $tds = $lease->tds_applicable === 'Yes' ? round((float) $data['rent_amount'] * (float) $lease->tds_rate / 100, 2) : 0;
        $schedule->fill($data + ['tds_amount' => $tds, 'net_payable' => round((float) $data['rent_amount'] - $tds, 2)])->save();
        $allocations->allocate($schedule->fresh());

        return $this->ok($schedule->auditLabel().' saved · split re-calculated');
    }

    /** Landowner-wise payment history of one instalment (modal). */
    public function payments(RentSchedule $schedule)
    {
        $this->authorizeSchedule($schedule);
        $schedule->load(['allocations.party', 'allocations.payments', 'lease', 'plot']);

        return response()->json([
            'title' => 'Landowner payment history · '.$schedule->auditLabel(),
            'html' => view('schedules.payments-modal', ['s' => $schedule])->render(),
        ]);
    }

    private function query(Request $request): Builder
    {
        $today = now()->toDateString();
        $soon = now()->addDays(30)->toDateString();

        return RentSchedule::visibleTo()
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->whereHas('lease', fn ($l) => $l->where('lease_no', 'like', "%$s%"))
                ->orWhereHas('plot', fn ($p) => $p->where('land_ref_no', 'like', "%$s%"))))
            ->when($request->lease_id, fn ($q, $v) => $q->where('lease_id', $v))
            ->when($request->park_id, fn ($q, $v) => $q->whereHas('lease', fn ($l) => $l->where('park_id', $v)))
            ->when($request->from, fn ($q, $v) => $q->whereDate('due_date', '>=', $v))
            ->when($request->to, fn ($q, $v) => $q->whereDate('due_date', '<=', $v))
            ->when($request->status, function ($q, $st) use ($today, $soon) {
                $unpaid = fn ($q) => $q->where('status', 'Pending')->whereNotNull('ownership_version_id');

                return match ($st) {
                    'Overdue' => $q->whereIn('status', ['Pending', 'Partially paid'])->whereDate('due_date', '<', $today),
                    'Due soon' => $unpaid($q)->whereDate('due_date', '>=', $today)->whereDate('due_date', '<=', $soon),
                    'Upcoming' => $unpaid($q)->whereDate('due_date', '>', $soon),
                    'Ownership not finalised' => $q->where('status', 'Pending')->whereNull('ownership_version_id'),
                    default => $q->where('status', $st),
                };
            });
    }

    private function authorizeSchedule(RentSchedule $schedule): void
    {
        abort_unless(auth()->user()->canAccessSpv($schedule->lease->spv_id), 403);
    }
}
