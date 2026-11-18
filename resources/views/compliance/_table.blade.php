<div class="tbl-wrap"><table>
  <thead><tr><th>Land ref no.</th><th>Mortgage</th><th>Lender · CERSAI</th><th>Originals</th><th>Lender NOC</th><th>Open PDC</th><th>Escalation</th><th>Evidence</th><th></th></tr></thead>
  <tbody>
  @forelse ($rows as $c)
    <tr>
      <td><a class="reflink" href="{{ route('plots.show', [$c->plot_id, 'tab' => 'compliance']) }}">{{ $c->plot->land_ref_no }}</a></td>
      <td>@if ($c->mortgage_created === 'Yes'){{ chip('Mortgaged') }}<span class="sub">{{ fmt_date($c->mortgage_date) }}</span>@else{{ chip('No') }}@endif</td>
      <td>{{ $c->lender_name ?: '—' }}<span class="sub mono">{{ $c->cersai_reg_no }}</span></td>
      <td>{{ $c->original_docs_custody }}<span class="sub">{{ $c->custodian_name }}</span></td>
      <td>{{ chip($c->lender_noc_status) }}</td>
      <td class="num">{{ $c->pdc_open_items }}<span class="sub" @if ($c->isOverdue()) style="color:var(--bad);font-weight:600" @endif>{{ $c->isOverdue() ? 'Past target ' : 'by ' }}{{ fmt_date($c->target_closure_date) }}</span></td>
      <td>{{ chip($c->escalation_level) }}</td>
      <td>{{ $c->documents_count ?? $c->documents()->count() }} docs</td>
      <td class="act">
        @if (can_do('document', 'add'))<button class="lnk" type="button" data-drawer="{{ route('documents.create', ['plot_id' => $c->plot_id, 'linked' => 'Compliance', 'record' => $c->id, 'doc_type' => 'CERSAI certificate']) }}">Add evidence</button>@endif
        <x-row-actions module="compliance" label="this compliance record" :history="route('history', ['compliance', $c->id])" :edit="route('compliance.edit', $c)" :delete="route('compliance.destroy', $c)" />
      </td>
    </tr>
  @empty
    <tr><td colspan="9" class="empty">{{ $emptyText ?? 'No compliance records yet.' }}</td></tr>
  @endforelse
  </tbody>
</table></div>
