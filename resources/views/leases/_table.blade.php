{{-- Lease rows. $rows: leases with plot, sub_active_mw (optional) --}}
<div class="tbl-wrap"><table>
  <thead><tr><th>Lease</th><th>Land ref no.</th><th>Lessors (landowners)</th><th class="num">Area</th><th class="num">Capacity</th><th>Term</th><th class="num">Rent</th><th>Status</th><th></th></tr></thead>
  <tbody>
  @forelse ($rows as $l)
    @php($sub = (float) ($l->sub_active_mw ?? 0))
    <tr>
      <td><span class="ref">{{ $l->lease_no }}</span><span class="sub">{{ $l->agreement_no }}</span></td>
      <td><a class="reflink" href="{{ route('plots.show', [$l->plot_id, 'tab' => 'leases']) }}">{{ $l->plot->land_ref_no }}</a><span class="sub">{{ $l->park->park_code ?? '' }} · {{ $l->spv->spv_code ?? '' }}</span></td>
      <td>
        @php($sh = $l->plot->currentOwnership?->shares)
        @if ($sh && $sh->count()){{ $sh->first()->party->party_name }}@if ($sh->count() > 1)<span class="sub">+{{ $sh->count() - 1 }} more</span>@endif
        @else<span class="hint">Ownership not finalised</span>@endif
      </td>
      <td class="num">{{ fmt_num($l->lease_area_acres, 4) }} ac</td>
      <td class="num"><b>{{ fmt_mw($l->leased_capacity_mw) }}</b><span class="sub">{{ fmt_mw($sub) }} sub-leased · {{ fmt_mw((float) $l->leased_capacity_mw - $sub) }} free today</span></td>
      <td>{{ fmt_date($l->lease_start_date) }}<span class="sub">to {{ fmt_date($l->lease_end_date) }}</span></td>
      <td class="num">{{ inr($l->annual_rent) }} / yr<span class="sub">{{ $l->payment_frequency }} · +{{ fmt_num($l->escalation_pct) }}% every {{ $l->escalation_every_yrs }} yrs</span></td>
      <td>{{ chip($l->status) }}</td>
      <td class="act">
        @if (can_do('schedule'))<a class="lnk" href="{{ route('schedules.index', ['lease_id' => $l->id]) }}">Schedule</a>@endif
        @if ($l->status === 'Active' && can_do('sublease', 'add'))<button class="lnk" type="button" data-drawer="{{ route('subleases.create', ['lease_id' => $l->id]) }}">Sub-lease</button>@endif
        <x-row-actions module="lease" :label="$l->lease_no" :history="route('history', ['lease', $l->id])" :edit="route('leases.edit', $l)" :delete="route('leases.destroy', $l)" />
      </td>
    </tr>
  @empty
    <tr><td colspan="9" class="empty">{{ $emptyText ?? 'No leases yet.' }}</td></tr>
  @endforelse
  </tbody>
</table></div>
