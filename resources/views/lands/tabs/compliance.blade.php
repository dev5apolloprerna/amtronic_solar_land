@include('lands.partials.tabhead', ['table' => 'COMPLIANCE_TRACKER', 'desc' => 'One record per land parcel: mortgage and CERSAI registration, custody of original documents, lender NOC and open pre-disbursement conditions.',
  'addUrl' => $total ? null : route('compliance.create', $land), 'addLabel' => 'Add compliance'])
@include('lands.partials.bar', ['what' => 'compliance'])
@php($ev = $land->documents()->where('linked_entity_type', 'Compliance')->get()->countBy('linked_entity_id'))
<div class="tbl-wrap"><table>
  <thead><tr><th>ID</th><th>Mortgage</th><th>Lender</th><th>Originals</th><th>NOC</th><th>Open PDC</th><th>Escalation</th><th>Evidence</th><th></th></tr></thead>
  <tbody>
  @forelse ($rows as $c)
    <tr>
      <td><span class="ref">{{ $c->code }}</span></td>
      <td>@if ($c->mortgage_created === 'Yes'){{ chip('Mortgaged') }}<span class="sub">{{ fmt_date($c->mortgage_date) }}</span>@else{{ chip('No') }}@endif</td>
      <td>{{ $c->lender_name ?: '—' }}<span class="sub mono">{{ $c->cersai_reg_no }}</span></td>
      <td>{{ $c->original_docs_custody }}</td>
      <td>{{ chip($c->lender_noc_status) }}</td>
      <td><span class="num">{{ $c->pdc_open_items }}</span><span class="sub" @if ($c->isLate()) style="color:var(--bad);font-weight:600" @endif>{{ $c->isLate() ? 'Past target ' : 'by ' }}{{ fmt_date($c->target_closure_date) }}</span></td>
      <td>{{ chip($c->escalation_level) }}</td>
      <td>{{ $ev[$c->id] ?? 0 }} docs</td>
      <td class="act">
        @if (can_do('doc', 'add'))<button class="lnk" type="button" data-drawer="{{ route('documents.create', [$land, 'type' => 'Compliance', 'id' => $c->id, 'doc_type' => 'CERSAI certificate']) }}">Add evidence</button>@endif
        @include('lands.partials.actions', ['module' => 'compliance', 'hist' => $c->id, 'edit' => route('compliance.edit', $c), 'delete' => route('compliance.destroy', $c), 'label' => $c->code])
      </td>
    </tr>
  @empty
    <tr><td colspan="9" class="empty">{{ $total ? 'No records match the search or filters.' : 'No compliance records yet'.(can_do('compliance', 'add') ? '. Use “Add compliance” to create the first one.' : '.') }}</td></tr>
  @endforelse
  </tbody>
</table></div>
