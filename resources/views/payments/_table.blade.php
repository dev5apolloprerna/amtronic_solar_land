<div class="tbl-wrap"><table>
  <thead><tr><th>Payment</th><th>Landowner</th><th>Land ref · instalment</th><th>Paid on</th><th class="num">Gross</th><th class="num">TDS</th><th class="num">Net paid</th><th>Mode · UTR</th><th></th></tr></thead>
  <tbody>
  @forelse ($rows as $p)
    @php($s = $p->allocation->schedule)
    <tr>
      <td><span class="ref">{{ $p->payment_no }}</span><span class="sub">{{ $p->sap_voucher_no }}</span></td>
      <td>{{ $p->allocation->party->party_name }}<span class="sub">{{ fmt_num($p->allocation->share_percent, 4) }}% share</span></td>
      <td><a class="reflink" href="{{ route('plots.show', [$s->plot_id, 'tab' => 'payments']) }}">{{ $s->plot->land_ref_no ?? '' }}</a><span class="sub">{{ $s->lease->lease_no }} · due {{ fmt_date($s->due_date) }}</span></td>
      <td>{{ fmt_date($p->payment_date) }}</td>
      <td class="num">{{ inr($p->gross_amount, 2) }}</td>
      <td class="num">{{ inr($p->tds_deducted, 2) }}<span class="sub">{{ $p->tds_challan_no }}</span></td>
      <td class="num"><b>{{ inr($p->net_paid, 2) }}</b></td>
      <td>{{ $p->payment_mode }}<span class="sub mono">{{ $p->utr_reference }}</span></td>
      <td class="act"><x-row-actions module="payment" :label="$p->payment_no" :history="route('history', ['payment', $p->id])" :edit="route('payments.edit', $p)" :delete="route('payments.destroy', $p)"
        deleteMessage="The landowner's paid amount and the instalment status are recalculated. The deletion is kept in the audit trail." /></td>
    </tr>
  @empty
    <tr><td colspan="9" class="empty">{{ $emptyText ?? 'No payments recorded yet.' }}</td></tr>
  @endforelse
  </tbody>
</table></div>
