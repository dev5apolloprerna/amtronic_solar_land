@include('lands.partials.tabhead', ['table' => 'RENT_SCHEDULE', 'desc' => 'Installment-level totals, paid and remaining balance. Use Payment History to inspect farmer-wise payments and edit individual payment transactions.'])
@include('lands.partials.bar', ['what' => 'rent schedule', 'slot' => new \Illuminate\Support\HtmlString(
  view('components.filter-select', ['name' => 'lease', 'label' => 'All leases', 'options' => $leaseOpts])->render().
  view('components.filter-select', ['name' => 'status', 'label' => 'All statuses', 'options' => opts('schedule_status')])->render())])
<div class="tbl-wrap tall"><table>
  <thead><tr><th>ID</th><th>Lease</th><th>Month</th><th>Due date</th><th>Rent</th><th>TDS</th><th>Net payable</th><th>Paid / balance</th><th>Remaining %</th><th>Status</th><th>Alert</th><th></th></tr></thead>
  <tbody>
  @forelse ($rows as $s)
    @php($paid = $s->totalPaid())
    @php($rent = (float) $s->rent_amount)
    @php($st = $s->status())
    <tr>
      <td><span class="ref">{{ $s->code }}</span></td>
      <td><span class="ref">{{ $s->lease->code }}</span></td>
      <td>{{ $s->due_month }}</td>
      <td>{{ fmt_date($s->due_date) }}</td>
      <td><span class="num">{{ inr($rent) }}</span></td>
      <td><span class="num">{{ inr($s->tds_amount) }}</span></td>
      <td><span class="num"><b>{{ inr($s->net_payable) }}</b></span></td>
      <td><b>{{ inr($paid) }}</b><span class="sub">Remaining {{ inr($s->remaining()) }}</span></td>
      <td><b>{{ $rent ? number_format(max(0, ($rent - $paid) / $rent * 100), 1) : 0 }}%</b><span class="sub">{{ $rent ? number_format(min(100, $paid / $rent * 100), 1) : 0 }}% paid</span></td>
      <td>{{ chip($st) }}</td>
      <td>{{ chip($s->alert_sent_flag) }}</td>
      <td class="act">
        @if ($st !== 'Paid' && can_do('payment', 'add'))<button class="lnk" type="button" data-drawer="{{ route('payments.create', [$land, 'schedule_id' => $s->id]) }}">Record payment</button>@endif
        <button class="lnk" type="button" data-history="{{ route('schedules.history', $s) }}">Payment History</button>
        <button class="lnk" type="button" data-drawer="{{ route('schedules.edit', $s) }}">{{ can_do('schedule', 'edit') ? 'Edit' : 'View' }}</button>
      </td>
    </tr>
  @empty
    <tr><td colspan="12" class="empty">{{ $total ? 'No records match the search or filters.' : 'No rent schedule records yet. Saving a lease generates its schedule.' }}</td></tr>
  @endforelse
  </tbody>
</table></div>
