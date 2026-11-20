{{-- Payment History modal for one instalment --}}
<p class="hint" style="margin:0 0 10px">{{ $s->lease->code }} · {{ $s->land->land_ref_no }} · due {{ fmt_date($s->due_date) }} · rent {{ inr($s->rent_amount) }} · TDS {{ inr($s->tds_amount) }} · {{ chip($s->status()) }}</p>
<div class="tbl-wrap"><table>
  <thead><tr><th>Farmer</th><th>Ownership</th><th>Gross share</th><th>Paid</th><th>Remaining</th></tr></thead>
  <tbody>
  @foreach ($shares as $x)
    <tr><td><b>{{ $x['farmer']->owner_name }}</b></td><td>{{ fmt_num($x['pct']) }}%</td><td>{{ inr($x['share']) }}</td><td>{{ inr($x['paid']) }}</td><td><b>{{ inr($x['remaining']) }}</b></td></tr>
  @endforeach
    <tr class="tot"><td>Total</td><td>{{ fmt_num($shares->sum('pct')) }}%</td><td>{{ inr($shares->sum('share')) }}</td><td>{{ inr($shares->sum('paid')) }}</td><td>{{ inr($shares->sum('remaining')) }}</td></tr>
  </tbody>
</table></div>
<h4 style="margin:14px 0 6px">Payments</h4>
<div class="tbl-wrap"><table>
  <thead><tr><th>Payment</th><th>Farmer</th><th>Paid on</th><th>Gross</th><th>TDS</th><th>Net</th><th>UTR</th><th></th></tr></thead>
  <tbody>
  @forelse ($s->payments as $p)
    <tr>
      <td><span class="ref">{{ $p->code }}</span></td><td>{{ $p->farmer?->owner_name ?? 'Legacy payment' }}</td><td>{{ fmt_date($p->payment_date) }}</td>
      <td>{{ inr($p->gross_amount) }}</td><td>{{ inr($p->tds_deducted) }}</td><td><b>{{ inr($p->net_paid) }}</b></td><td class="mono">{{ $p->utr_reference }}</td>
      <td class="act">
        @if (can_do('payment', 'view'))<button class="lnk" type="button" data-act="modal-close" data-drawer="{{ route('payments.edit', $p) }}">{{ can_do('payment', 'edit') ? 'Edit' : 'View' }}</button>@endif
        @if (can_do('payment', 'delete'))<button class="lnk red" type="button" data-delete-url="{{ route('payments.destroy', $p) }}" data-label="{{ $p->code }}">Delete</button>@endif
      </td>
    </tr>
  @empty
    <tr><td colspan="8" class="empty">No payments recorded against this instalment yet.</td></tr>
  @endforelse
  </tbody>
</table></div>
