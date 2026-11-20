<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LandAccess;
use App\Http\Requests\PaymentRequest;
use App\Models\Land;
use App\Models\Payment;
use App\Models\RentSchedule;
use App\Services\ShareService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** PAYMENT_REGISTER · one row per farmer per instalment */
class PaymentController extends Controller
{
    use LandAccess;

    public function __construct(private ShareService $shares) {}

    public function create(Request $request, Land $land)
    {
        $this->guardLand($land);
        $land->load('farmers');
        if (! $this->shares->ownershipOk($land)) {
            return $this->refuse('Farmer ownership of '.$land->land_ref_no.' totals '.fmt_num($land->ownershipTotal()).'%. It must total 100% before recording payments.');
        }
        $open = $land->schedules()->with(['payments', 'lease'])->orderBy('due_date')->get()->filter(fn ($s) => $s->remaining() > 0.009);
        if ($open->isEmpty()) {
            return $this->refuse('There are no unpaid rent instalments on '.$land->land_ref_no.'.');
        }
        $s = $open->firstWhere('id', (int) $request->schedule_id) ?? $open->first();
        $rows = $this->shares->forSchedule($s->setRelation('land', $land));
        $farmer = $rows->firstWhere(fn ($r) => $r['farmer']->id == $request->farmer_id) ?? $rows->first(fn ($r) => $r['remaining'] > 0.009);
        $p = new Payment([
            'schedule_id' => $s->id, 'farmer_id' => $farmer['farmer']->id ?? null, 'payment_date' => now()->toDateString(),
            'gross_amount' => $farmer['remaining'] ?? null,
            'tds_deducted' => $farmer ? min($farmer['tds'], $farmer['remaining']) : null,
        ]);

        return $this->drawer('Record payment', 'PAYMENT_REGISTER · '.$land->land_ref_no, 'lands.forms.payment', [
            'p' => $p, 'land' => $land, 'schedule' => $s, 'shares' => $rows,
            'scheduleOpts' => $open->mapWithKeys(fn ($x) => [$x->id => $x->code.' · '.$x->lease->code.' · '.fmt_date($x->due_date).' · '.inr($x->rent_amount).' · '.$x->status()])->all(),
        ], module: 'payment', saveLabel: 'Record payment');
    }

    public function store(PaymentRequest $request, Land $land)
    {
        $this->guardLand($land);
        $d = $request->validated();
        $s = RentSchedule::findOrFail($d['schedule_id']);
        abort_unless($s->land_id === $land->id, 422, 'That instalment is not on this parcel.');
        $p = DB::transaction(function () use ($d, $s) {
            $d['tds_deducted'] = (float) ($d['tds_deducted'] ?? 0);
            $p = Payment::create($d + ['land_id' => $s->land_id, 'net_paid' => (float) $d['gross_amount'] - $d['tds_deducted']]);
            $s->refreshStatus();

            return $p;
        });

        return $this->ok("{$p->code} recorded · {$s->code} is now ".$s->fresh()->status(), $this->tabUrl($land, 'payment'));
    }

    public function edit(Payment $payment)
    {
        $land = $payment->land;
        $this->guardLand($land);
        $ro = ! $this->canEdit('payment');
        $s = $payment->schedule()->with(['payments', 'lease'])->first();

        return $this->drawer(($ro ? 'View' : 'Edit').' payment', 'PAYMENT_REGISTER · '.$payment->code, 'lands.forms.payment', [
            'p' => $payment, 'land' => $land, 'schedule' => $s, 'shares' => $this->shares->forSchedule($s->setRelation('land', $land->load('farmers'))),
            'scheduleOpts' => [$s->id => $s->code.' · '.$s->lease->code.' · '.fmt_date($s->due_date).' · '.inr($s->rent_amount)],
        ], $ro, 'payment');
    }

    public function update(PaymentRequest $request, Payment $payment)
    {
        $this->guardLand($payment->land);
        $d = $request->validated();
        $d['tds_deducted'] = (float) ($d['tds_deducted'] ?? 0);
        $d['net_paid'] = (float) $d['gross_amount'] - $d['tds_deducted'];
        $payment->update($d);
        $payment->schedule->refreshStatus();

        return $this->ok('Payment saved', $this->tabUrl($payment->land, 'payment'));
    }

    public function destroy(Payment $payment)
    {
        $this->guardLand($payment->land);
        $s = $payment->schedule;
        $payment->delete();
        $s->refreshStatus();

        return $this->ok("{$payment->code} deleted · {$s->code} is now ".$s->fresh()->status(), $this->tabUrl($payment->land, 'payment'));
    }
}
