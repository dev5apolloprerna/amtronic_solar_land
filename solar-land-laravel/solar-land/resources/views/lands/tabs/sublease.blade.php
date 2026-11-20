@include('lands.partials.tabhead', ['table' => 'SUBLEASE_MASTER', 'desc' => 'Subleases carved out of a lease. The total subleased area can never exceed the leased area: a 100-acre lease allows at most 100 acres across all its subleases. Likewise, active subleases running on the same day can never exceed the lease’s capacity: a 200 MW lease with 100 MW subleased shows 100 MW remaining.',
  'addUrl' => $land->leases()->whereIn('lease_status', ['Active', 'Draft'])->exists() ? route('subleases.create', $land) : null, 'addLabel' => 'Add sublease'])
@foreach ($land->leases()->whereIn('lease_status', ['Active', 'Draft'])->orderBy('id')->get() as $L)
  @php($used = $cap->areaUsed($L))
  @php($mw = $cap->mwToday($L))
  @php($ac = (float) $L->leased_area_acres)
  @php($tmw = (float) $L->leased_capacity_mw)
  <section class="panel"><h3>Capacity of {{ $L->code }} <span class="sub">{{ chip($L->lease_status) }}</span></h3><div class="pb">
    <div class="util"><div class="util-h"><b>Area</b><span>{{ fmt_num($used) }} / {{ fmt_num($ac) }} ac subleased · <b>{{ fmt_num(max(0, $ac - $used)) }} ac free</b></span></div>
      <div class="bar"><span class="seg cur" style="width:{{ $ac ? min(100, $used / $ac * 100) : 0 }}%"></span></div></div>
    <div class="util"><div class="util-h"><b>Capacity</b><span>{{ fmt_num($mw, 3) }} / {{ fmt_num($tmw, 3) }} MW subleased today · <b>{{ fmt_num(max(0, $tmw - $mw), 3) }} MW remaining</b></span></div>
      <div class="bar"><span class="seg cur" style="width:{{ $tmw ? min(100, $mw / $tmw * 100) : 0 }}%"></span></div></div>
  </div></section>
@endforeach
@include('lands.partials.bar', ['what' => 'sublease'])
<div class="tbl-wrap"><table>
  <thead><tr><th>Sublease</th><th>Lease</th><th>Sublessee</th><th>Area (ac)</th><th>Capacity (MW)</th><th>Period</th><th>Rent / yr</th><th>Lender NOC</th><th>Status</th><th></th></tr></thead>
  <tbody>
  @forelse ($rows as $s)
    <tr>
      <td><span class="ref">{{ $s->code }}</span></td>
      <td><span class="ref">{{ $s->lease->code }}</span></td>
      <td>{{ $s->sublessee_name }}<span class="sub mono">{{ $s->sublessee_pan }}</span></td>
      <td><span class="num">{{ fmt_num($s->sublease_area_acres) }}</span><span class="sub">of {{ fmt_num($s->lease->leased_area_acres) }} leased</span></td>
      <td><span class="num">{{ fmt_num($s->capacity_mw, 3) }}</span><span class="sub">of {{ fmt_num($s->lease->leased_capacity_mw, 3) }} MW</span></td>
      <td>{{ fmt_date($s->sublease_start) }}<span class="sub">to {{ fmt_date($s->sublease_end) }}</span></td>
      <td><span class="num">{{ inr($s->sublease_rent) }}</span></td>
      <td>{{ chip($s->lender_noc_obtained) }}</td>
      <td>{{ chip($s->status) }}</td>
      <td class="act">@include('lands.partials.actions', ['module' => 'sublease', 'hist' => $s->id, 'edit' => route('subleases.edit', $s), 'delete' => route('subleases.destroy', $s), 'label' => $s->code])</td>
    </tr>
  @empty
    <tr><td colspan="10" class="empty">{{ $total ? 'No records match the search or filters.' : 'No sublease records yet'.(can_do('sublease', 'add') ? '. Use “Add sublease” to create the first one.' : '.') }}</td></tr>
  @endforelse
  </tbody>
</table></div>
