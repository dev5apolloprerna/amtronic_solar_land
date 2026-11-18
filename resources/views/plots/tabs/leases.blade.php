<div class="tabhead">
  <div><span class="tbl-name">LEASES</span><h2>Leases</h2><p>Lease agreements for this survey number. Only one can be active at a time. Saving an active lease generates its rent schedule.</p></div>
  <div class="actions">@if (can_do('lease', 'add') && ! $lease)<button class="btn pri" type="button" data-drawer="{{ route('leases.create', ['plot_id' => $plot->id]) }}">Add lease</button>@endif</div>
</div>
@if (! $plot->currentOwnership)<div class="note-b">Landowner shares are not finalised yet. Rent instalments will show “Ownership not finalised” and accept no payments until they are.</div>@endif
@php($rows = $leases->each(fn ($l) => $l->setRelation('plot', $plot)->setAttribute('sub_active_mw', app(\App\Services\CapacityService::class)->subLeasedOn($l))))
@include('leases._table', ['emptyText' => 'No leases on this survey number yet.'])
