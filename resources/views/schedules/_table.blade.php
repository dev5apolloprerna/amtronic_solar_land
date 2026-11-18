<div class="tbl-wrap tall"><table>
  <thead><tr><th>Instalment</th><th>Lease · land ref</th><th>Period</th><th>Due date</th><th class="num">Rent</th><th class="num">TDS</th><th class="num">Net payable</th><th>Paid / balance</th><th>Status</th><th></th></tr></thead>
  <tbody>
  @forelse ($rows as $s)
    @php($paid = $s->paidTotal())
    @php($st = $s->displayStatus())
    <tr>
      <td><span class="ref">{{ $s->auditLabel() }}</span><span class="sub">#{{ $s->installment_no }}</span></td>
      <td><span class="ref">{{ $s->lease->lease_no }}</span><span class="sub"><a class="reflink" href="{{ route('plots.show', [$s->plot_id, 'tab' => 'schedule']) }}">{{ $s->plot->land_ref_no }}</a></span></td>
      <td>{{ fmt_date($s->period_from) }}<span class="sub">to {{ fmt_date($s->period_to) }}</span></td>
      <td>{{ fmt_date($s->due_date) }}</td>
      <td class="num">{{ inr($s->rent_amount, 2) }}</td>
      <td class="num">{{ inr($s->tds_amount, 2) }}</td>
      <td class="num"><b>{{ inr($s->net_payable, 2) }}</b></td>
      <td><b>{{ inr($paid, 2) }}</b><span class="sub">Remaining {{ inr($s->remaining(), 2) }} · {{ (float) $s->rent_amount > 0 ? number_format($s->remaining() / (float) $s->rent_amount * 100, 1) : '0.0' }}%</span></td>
      <td>{{ chip($st, $st === 'Ownership not finalised' ? 'bad' : null) }}@if ($s->alert_sent === 'Yes')<span class="sub">alert sent</span>@endif</td>
      <td class="act">
        <button class="lnk" type="button" data-history="{{ route('schedules.payments', $s) }}">Payment history</button>
        @if ($s->status !== 'Paid' && $s->allocations->isNotEmpty() && can_do('payment', 'add'))
          <button class="lnk" type="button" data-drawer="{{ route('payments.create', ['schedule_id' => $s->id]) }}">Record payment</button>
        @endif
        <button class="lnk" type="button" data-drawer="{{ route('schedules.edit', $s) }}">{{ can_do('schedule', 'edit') && in_array($s->status, ['Pending', 'On hold']) ? 'Edit' : 'View' }}</button>
      </td>
    </tr>
  @empty
    <tr><td colspan="10" class="empty">{{ $emptyText ?? 'No rent instalments yet. They are generated when an active lease is saved.' }}</td></tr>
  @endforelse
  </tbody>
</table></div>
