@include('lands.partials.tabhead', ['table' => 'LEASE_MASTER', 'desc' => 'Lease agreements with the landowner. Saving a new lease generates its full rent schedule with escalation and TDS. Each lease also carries the electricity capacity (MW) that can be subleased.',
  'addUrl' => route('leases.create', $land), 'addLabel' => 'Add lease'])
@include('lands.partials.bar', ['what' => 'lease'])
<div class="tbl-wrap"><table>
  <thead><tr><th>Lease</th><th>Lessor</th><th>Area (ac)</th><th>Capacity (MW)</th><th>Term</th><th>Rent / instalment</th><th>Subleased</th><th>Status</th><th></th></tr></thead>
  <tbody>
  @forelse ($rows as $r)
    @php($used = $cap->areaUsed($r))
    @php($mw = $cap->mwToday($r))
    <tr>
      <td><span class="ref">{{ $r->code }}</span></td>
      <td>{{ $r->lessor_name }}<span class="sub">{{ $r->lessorCity?->city_name }}, {{ $r->lessorDistrict?->district_name }}</span></td>
      <td><span class="num">{{ fmt_num($r->leased_area_acres) }}</span></td>
      <td><span class="num">{{ fmt_num($r->leased_capacity_mw, 3) }}</span></td>
      <td>{{ fmt_date($r->lease_start_date) }}<span class="sub">to {{ fmt_date($r->lease_end_date) }}</span></td>
      <td><span class="num">{{ inr($r->rent_amount) }}</span><span class="sub">{{ $r->rent_frequency }} · +{{ fmt_num($r->escalation_pct) }}% every {{ $r->escalation_every_yrs }} yrs</span></td>
      <td>{{ fmt_num($used) }} / {{ fmt_num($r->leased_area_acres) }} ac<span class="sub">{{ fmt_num($mw, 3) }} / {{ fmt_num($r->leased_capacity_mw, 3) }} MW · {{ fmt_num(max(0, (float) $r->leased_capacity_mw - $mw), 3) }} MW free</span></td>
      <td>{{ chip($r->lease_status) }}</td>
      <td class="act">
        <a class="lnk" href="{{ route('lands.show', [$land, 'tab' => 'schedule', 'lease' => $r->id]) }}">Schedule</a>
        @if (can_do('sublease', 'add') && $r->lease_status !== 'Terminated' && $r->lease_status !== 'Expired')<button class="lnk" type="button" data-drawer="{{ route('subleases.create', [$land, 'lease_id' => $r->id]) }}">Sublease</button>@endif
        @include('lands.partials.actions', ['module' => 'lease', 'hist' => $r->id, 'edit' => route('leases.edit', $r), 'delete' => route('leases.destroy', $r), 'label' => $r->code,
          'msg' => 'This removes the lease and its unpaid rent schedule. Leases with payments cannot be deleted; set them to Terminated instead.'])
      </td>
    </tr>
  @empty
    <tr><td colspan="9" class="empty">{{ $total ? 'No records match the search or filters.' : 'No lease records yet'.(can_do('lease', 'add') ? '. Use “Add lease” to create the first one.' : '.') }}</td></tr>
  @endforelse
  </tbody>
</table></div>
