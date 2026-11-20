@include('lands.partials.tabhead', ['table' => 'PAYMENT_REGISTER', 'desc' => 'Select a farmer and pay any amount up to their remaining ownership-based rent. Multiple payments and partially paid schedules are supported.',
  'addUrl' => route('payments.create', $land), 'addLabel' => 'Record payment'])
@if ($progress->isNotEmpty())
  <section class="panel">
    <h3>Farmer-wise payment progress · Each instalment</h3>
    <div class="tbl-wrap tall"><table>
      <thead><tr><th>Instalment / farmer</th><th>Ownership</th><th>Gross share</th><th>Paid</th><th>Remaining</th><th>Remaining %</th><th>Status / action</th></tr></thead>
      <tbody>
      @foreach ($progress as $p)
        @foreach ($p['shares'] as $x)
          @php($st = $x['paid'] == 0 ? 'Pending' : ($x['remaining'] < 0.01 ? 'Paid' : 'Partial Payment'))
          <tr>
            <td><b>{{ $x['farmer']->owner_name }}</b><span class="sub">{{ $p['s']->code }} · {{ fmt_date($p['s']->due_date) }}</span></td>
            <td>{{ fmt_num($x['pct']) }}%</td>
            <td>{{ inr($x['share']) }}</td>
            <td>{{ inr($x['paid']) }}</td>
            <td><b>{{ inr($x['remaining']) }}</b></td>
            <td>{{ $x['share'] ? number_format($x['remaining'] / $x['share'] * 100, 1) : '0.0' }}%</td>
            <td>{{ chip($st) }}
              @if ($x['remaining'] > 0 && can_do('payment', 'add'))<button class="lnk" type="button" data-drawer="{{ route('payments.create', [$land, 'schedule_id' => $p['s']->id, 'farmer_id' => $x['farmer']->id]) }}">Pay farmer</button>@endif
            </td>
          </tr>
        @endforeach
      @endforeach
      </tbody>
    </table></div>
  </section>
@endif
@include('lands.partials.bar', ['what' => 'payments'])
<div class="tbl-wrap"><table>
  <thead><tr><th>Payment</th><th>Instalment</th><th>Farmer</th><th>Paid on</th><th>Gross paid</th><th>TDS</th><th>Net paid</th><th>UTR</th><th></th></tr></thead>
  <tbody>
  @forelse ($rows as $p)
    <tr>
      <td><span class="ref">{{ $p->code }}</span></td>
      <td><span class="ref">{{ $p->schedule->code }}</span></td>
      <td>{{ $p->farmer?->owner_name ?? 'Legacy payment' }}</td>
      <td>{{ fmt_date($p->payment_date) }}</td>
      <td>{{ inr($p->gross_amount) }}</td>
      <td>{{ inr($p->tds_deducted) }}</td>
      <td><b>{{ inr($p->net_paid) }}</b></td>
      <td>{{ $p->utr_reference }}</td>
      <td class="act">@include('lands.partials.actions', ['module' => 'payment', 'hist' => $p->id, 'edit' => route('payments.edit', $p), 'delete' => route('payments.destroy', $p), 'label' => $p->code])</td>
    </tr>
  @empty
    <tr><td colspan="9" class="empty">{{ $total ? 'No records match the search or filters.' : 'No payments records yet'.(can_do('payment', 'add') ? '. Use “Record payment” to create the first one.' : '.') }}</td></tr>
  @endforelse
  </tbody>
</table></div>
