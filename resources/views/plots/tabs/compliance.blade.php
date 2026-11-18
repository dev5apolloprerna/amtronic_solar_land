@php($c = $plot->compliance)
<div class="tabhead">
  <div><span class="tbl-name">COMPLIANCE_RECORDS</span><h2>Compliance</h2><p>One record per survey number: mortgage and CERSAI, custody of originals, lender NOC and open items.</p></div>
  <div class="actions">
    @if (! $c && can_do('compliance', 'add'))<button class="btn pri" type="button" data-drawer="{{ route('compliance.create', ['plot_id' => $plot->id]) }}">Add compliance record</button>@endif
  </div>
</div>
@php($rows = collect($c ? [$c->setRelation('plot', $plot)] : []))
@include('compliance._table', ['emptyText' => 'No compliance record for this survey number yet.'])
