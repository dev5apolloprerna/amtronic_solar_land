@php($cap = app(\App\Services\CapacityService::class))
<div class="tabhead">
  <div><span class="tbl-name">SUB_LEASES</span><h2>Sub-leases</h2><p>MW carved out of this survey number’s lease. Active sub-leases can never exceed the lease’s MW on any day.</p></div>
  <div class="actions">@if ($lease && can_do('sublease', 'add'))<button class="btn pri" type="button" data-drawer="{{ route('subleases.create', ['lease_id' => $lease->id]) }}">Add sub-lease</button>@endif</div>
</div>
@foreach ($plotLeases as $l)
  @php($used = $cap->subLeasedOn($l))
  @php($t = (float) $l->leased_capacity_mw)
  <div class="cap">
    <div class="cap-head"><b>Capacity on {{ $l->lease_no }} today</b><span class="mono">{{ fmt_mw($t) }} leased</span></div>
    <div class="bar" role="img" aria-label="{{ fmt_mw($used) }} of {{ fmt_mw($t) }} sub-leased"><span class="seg cur" style="width:{{ $t > 0 ? min(100, $used / $t * 100) : 0 }}%"></span></div>
    <div class="cap-leg"><span><i style="background:var(--accent)"></i>Sub-leased {{ fmt_mw($used) }}</span><span><i style="background:var(--sunk);border:1px solid var(--line)"></i>Remaining {{ fmt_mw($t - $used) }}</span></div>
  </div>
@endforeach
@if (! $lease)<div class="note-b">There is no active lease on this survey number, so nothing can be sub-leased.</div>@endif
@php($rows = $subleases)
@include('subleases._table', ['emptyText' => 'No sub-leases on this survey number yet.'])
