<?php

namespace App\Http\Controllers;

use App\Models\Park;
use App\Models\Party;
use App\Models\RentAllocation;
use App\Models\RentPayment;
use App\Models\RentSchedule;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/** Landowner-wise rent payments. Part payments allowed up to each owner's remaining share of an instalment. */
class RentPaymentController extends Controller
{
    public function __construct(private DocumentService $documents) {}

    public function index(Request $request)
    {
        $q = RentPayment::visibleTo()->with(['allocation.party', 'allocation.schedule.lease', 'allocation.schedule.plot'])
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('payment_no', 'like', "%$s%")->orWhere('utr_reference', 'like', "%$s%")
                ->orWhereHas('allocation.party', fn ($p) => $p->where('party_name', 'like', "%$s%"))
                ->orWhereHas('allocation.schedule.plot', fn ($p) => $p->where('land_ref_no', 'like', "%$s%"))))
            ->when($request->park_id, fn ($q, $v) => $q->whereHas('allocation.schedule.lease', fn ($l) => $l->where('park_id', $v)))
            ->when($request->party_id, fn ($q, $v) => $q->whereHas('allocation', fn ($a) => $a->where('party_id', $v)))
            ->when($request->from, fn ($q, $v) => $q->whereDate('payment_date', '>=', $v))
            ->when($request->to, fn ($q, $v) => $q->whereDate('payment_date', '<=', $v))
            ->latest('payment_date')->latest('id');

        if ($export = $this->maybeExport('payment', 'Rent Payment Register', ['Payment no.', 'Date', 'Landowner', 'PAN', 'Land ref no.', 'Lease', 'Instalment due', 'Gross', 'TDS', 'TDS challan', 'Net paid', 'Mode', 'UTR', 'SAP voucher'],
            $q->get()->map(fn ($p) => [$p->payment_no, $p->payment_date->format('Y-m-d'), $p->allocation->party->party_name, $p->allocation->party->pan, $p->allocation->schedule->plot->land_ref_no,
                $p->allocation->schedule->lease->lease_no, $p->allocation->schedule->due_date->format('Y-m-d'), (float) $p->gross_amount, (float) $p->tds_deducted, $p->tds_challan_no,
                (float) $p->net_paid, $p->payment_mode, $p->utr_reference, $p->sap_voucher_no]))) {
            return $export;
        }

        return view('payments.index', [
            'rows' => $q->paginate(config('solar.per_page'))->withQueryString(),
            'total' => RentPayment::visibleTo()->count(),
            'parks' => Park::orderBy('park_code')->pluck('park_code', 'id'),
            'owners' => Party::where('party_type', 'Landowner')->orderBy('party_name')->pluck('party_name', 'id'),
        ]);
    }

    public function create(Request $request)
    {
        $allocation = $request->allocation_id ? RentAllocation::with(['party', 'schedule.lease', 'schedule.plot'])->find($request->allocation_id) : null;
        $schedule = $allocation?->schedule ?? ($request->schedule_id ? RentSchedule::with(['lease', 'plot', 'allocations.party'])->find($request->schedule_id) : null);
        if ($schedule) {
            abort_unless(auth()->user()->canAccessSpv($schedule->lease->spv_id), 403);
        }
        $payment = new RentPayment(['payment_date' => now(), 'payment_mode' => 'NEFT', 'rent_allocation_id' => $allocation?->id]);

        return $this->drawer('Record payment', 'RENT_PAYMENTS', 'payments.form', [
            'payment' => $payment,
            'schedule' => $schedule,
            'allocation' => $allocation,
            'openSchedules' => $schedule ? [] : RentSchedule::visibleTo()->whereIn('status', ['Pending', 'Partially paid'])->has('allocations')->with(['lease', 'plot'])
                ->orderBy('due_date')->limit(500)->get()->mapWithKeys(fn ($s) => [$s->id => $s->auditLabel().' · '.$s->plot->land_ref_no.' · due '.fmt_date($s->due_date).' · '.inr($s->rent_amount)])->all(),
        ], module: 'payment', saveLabel: 'Record payment');
    }

    public function store(Request $request)
    {
        $data = $this->validatePayment($request);
        $payment = DB::transaction(function () use ($data, $request) {
            $allocation = RentAllocation::whereKey($data['rent_allocation_id'])->lockForUpdate()->firstOrFail();
            $this->authorizeAllocation($allocation);
            $this->assertWithinBalance($allocation, (float) $data['gross_amount'], 0);
            $payment = new RentPayment(collect($data)->except('proof')->all());
            $payment->payment_no = RentPayment::nextNumber();
            $payment->net_paid = round((float) $data['gross_amount'] - (float) ($data['tds_deducted'] ?? 0), 2);
            $payment->save();
            $this->settle($allocation);
            $this->attachProof($request, $payment, $allocation);

            return $payment;
        });
        $s = $payment->allocation->schedule->fresh('allocations');

        return $this->ok("{$payment->payment_no} recorded for {$payment->allocation->party->party_name}", null, $s->auditLabel().': '.$s->status.' · '.inr($s->remaining(), 2).' remaining');
    }

    public function edit(RentPayment $payment)
    {
        $allocation = $payment->allocation()->with(['party', 'schedule.lease', 'schedule.plot'])->first();
        $this->authorizeAllocation($allocation);
        $ro = ! $this->canEdit('payment');

        return $this->drawer(($ro ? 'View' : 'Edit').' payment', 'RENT_PAYMENTS · '.$payment->payment_no, 'payments.form',
            ['payment' => $payment, 'allocation' => $allocation, 'schedule' => $allocation->schedule, 'openSchedules' => []], $ro, 'payment');
    }

    public function update(Request $request, RentPayment $payment)
    {
        $data = $this->validatePayment($request, $payment);
        DB::transaction(function () use ($data, $payment, $request) {
            $allocation = RentAllocation::whereKey($payment->rent_allocation_id)->lockForUpdate()->firstOrFail();
            $this->authorizeAllocation($allocation);
            $this->assertWithinBalance($allocation, (float) $data['gross_amount'], (float) $payment->gross_amount);
            $payment->fill(collect($data)->except(['proof', 'rent_allocation_id'])->all());
            $payment->net_paid = round((float) $data['gross_amount'] - (float) ($data['tds_deducted'] ?? 0), 2);
            $payment->save();
            $this->settle($allocation);
            $this->attachProof($request, $payment, $allocation);
        });

        return $this->ok("{$payment->payment_no} saved");
    }

    public function destroy(RentPayment $payment)
    {
        $allocation = $payment->allocation;
        $this->authorizeAllocation($allocation);
        DB::transaction(function () use ($payment, $allocation) {
            $payment->delete();
            $this->settle($allocation);
        });

        return $this->ok("{$payment->payment_no} deleted · instalment status recalculated");
    }

    private function validatePayment(Request $request, ?RentPayment $payment = null): array
    {
        $data = $request->validate([
            'rent_allocation_id' => $payment ? ['nullable'] : ['required', 'exists:rent_allocations,id'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'gross_amount' => ['required', 'numeric', 'gt:0'],
            'tds_deducted' => ['nullable', 'numeric', 'min:0', 'lte:gross_amount'],
            'tds_challan_no' => ['nullable', 'string', 'max:40'],
            'payment_mode' => ['required', Rule::in(config('solar.options.payment_mode'))],
            'utr_reference' => ['required', 'string', 'max:60'],
            'sap_voucher_no' => ['nullable', 'string', 'max:40'],
            'remarks' => ['nullable', 'string', 'max:255'],
            'proof' => ['nullable', 'file', 'max:'.config('solar.upload.max_kb'), 'mimes:'.implode(',', config('solar.upload.extensions')), 'extensions:'.implode(',', config('solar.upload.extensions'))],
        ], [
            'rent_allocation_id.required' => 'Choose the landowner to pay.',
            'gross_amount.gt' => 'Payment amount must be greater than zero.',
            'tds_deducted.lte' => 'TDS cannot exceed the gross payment.',
            'payment_date.before_or_equal' => 'Payment date cannot be in the future.',
        ]);
        if ((float) ($data['tds_deducted'] ?? 0) > 0 && blank($data['tds_challan_no'] ?? null)) {
            throw ValidationException::withMessages(['tds_challan_no' => 'Enter the TDS challan number when TDS is deducted.']);
        }

        return $data;
    }

    private function assertWithinBalance(RentAllocation $allocation, float $gross, float $original): void
    {
        $remaining = round((float) $allocation->gross_amount - (float) $allocation->paid_amount + $original, 2);
        if ($gross > $remaining + 0.005) {
            throw ValidationException::withMessages(['gross_amount' => 'Payment exceeds '.$allocation->party->party_name.'’s remaining balance of '.inr(max(0, $remaining), 2).' for this instalment.']);
        }
    }

    private function settle(RentAllocation $allocation): void
    {
        $allocation->refreshPaid();
        $allocation->schedule->refreshStatus();
    }

    private function attachProof(Request $request, RentPayment $payment, RentAllocation $allocation): void
    {
        if ($request->hasFile('proof')) {
            $this->documents->store($request->file('proof'), [
                'plot_id' => $allocation->schedule->plot_id, 'documentable_type' => RentPayment::class, 'documentable_id' => $payment->id,
                'doc_type' => 'Payment proof', 'title' => 'Payment proof '.$payment->payment_no, 'is_original_scan' => 'No',
            ]);
        }
    }

    private function authorizeAllocation(RentAllocation $allocation): void
    {
        abort_unless(auth()->user()->canAccessSpv($allocation->schedule->lease->spv_id), 403);
    }
}
