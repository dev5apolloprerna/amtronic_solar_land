@php($total = (float) $s->rent_amount)
@php($paid = $s->paidTotal())
<div class="hint" style="margin-bottom:12px">{{ $s->plot->land_ref_no }} · {{ $s->lease->lease_no }} · Due {{ fmt_date($s->due_date) }}</div>
<div class="tiles" style="margin-bottom:16px">
  <div class="tile"><small>Total rent</small><b style="font-size:19px">{{ inr($total, 2) }}</b></div>
  <div class="tile"><small>Total paid</small><b style="font-size:19px">{{ inr($paid, 2) }}</b></div>
  <div class="tile"><small>Remaining</small><b style="font-size:19px">{{ inr($s->remaining(), 2) }}</b><span>{{ $total ? number_format($s->remaining() / $total * 100, 1) : '0.0' }}% remaining</span></div>
  <div class="tile"><small>Instalment status</small><div style="margin-top:10px">{{ chip($s->displayStatus()) }}</div></div>
</div>
<h4 style="margin:18px 0 8px">Landowner-wise payment summary</h4>
<div class="tbl-wrap"><table>
  <thead><tr><th>Landowner</th><th>Share</th><th class="num">Gross share</th><th class="num">Paid</th><th class="num">Balance</th><th>Status</th><th></th></tr></thead>
  <tbody>
  @forelse ($s->allocations->sortByDesc('share_percent') as $a)
    @php($st = (float) $a->paid_amount <= 0 ? 'Pending' : ($a->remaining() <= 0.01 ? 'Paid' : 'Partially paid'))
    <tr><td><b>{{ $a->party->party_name }}</b></td><td>{{ fmt_num($a->share_percent, 4) }}%</td><td class="num">{{ inr($a->gross_amount, 2) }}</td><td class="num">{{ inr($a->paid_amount, 2) }}</td>
      <td class="num"><b>{{ inr($a->remaining(), 2) }}</b></td><td>{{ chip($st) }}</td>
      <td>@if ($a->remaining() > 0.01 && can_do('payment', 'add'))<button class="lnk" type="button" data-act="modal-close" data-drawer="{{ route('payments.create', ['allocation_id' => $a->id]) }}">Pay</button>@endif</td></tr>
  @empty
    <tr><td colspan="7" class="empty">No finalised ownership in force on the due date, so this instalment has no landowner split yet.</td></tr>
  @endforelse
  </tbody>
</table></div>
<h4 style="margin:22px 0 8px">Payment transactions ({{ $s->allocations->flatMap->payments->count() }})</h4>
<div class="tbl-wrap"><table>
  <thead><tr><th>Payment</th><th>Landowner</th><th>Date</th><th class="num">Gross</th><th class="num">TDS</th><th class="num">Net</th><th>UTR</th><th></th></tr></thead>
  <tbody>
  @forelse ($s->allocations->flatMap(fn ($a) => $a->payments->map(fn ($p) => [$a, $p]))->sortByDesc(fn ($x) => $x[1]->payment_date) as [$a, $p])
    <tr><td class="ref">{{ $p->payment_no }}</td><td>{{ $a->party->party_name }}</td><td>{{ fmt_date($p->payment_date) }}</td><td class="num">{{ inr($p->gross_amount, 2) }}</td>
      <td class="num">{{ inr($p->tds_deducted, 2) }}</td><td class="num">{{ inr($p->net_paid, 2) }}</td><td class="mono">{{ $p->utr_reference }}</td>
      <td>@if (can_do('payment', 'edit'))<button class="lnk" type="button" data-act="modal-close" data-drawer="{{ route('payments.edit', $p) }}">Edit</button>@endif</td></tr>
  @empty
    <tr><td colspan="8" class="empty">No payments recorded yet.</td></tr>
  @endforelse
  </tbody>
</table></div>
