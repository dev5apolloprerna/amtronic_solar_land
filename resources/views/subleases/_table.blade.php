<div class="tbl-wrap"><table>
  <thead><tr><th>Sub-lease</th><th>Parent lease</th><th>Sub-lessee</th><th class="num">Capacity</th><th>Period</th><th class="num">Rent</th><th>Lender NOC</th><th>Status</th><th></th></tr></thead>
  <tbody>
  @forelse ($rows as $s)
    <tr>
      <td><span class="ref">{{ $s->sub_lease_no }}</span><span class="sub">{{ $s->agreement_no }}</span></td>
      <td><span class="ref">{{ $s->lease->lease_no }}</span><span class="sub"><a class="reflink" href="{{ route('plots.show', [$s->lease->plot_id, 'tab' => 'subleases']) }}">{{ $s->lease->plot->land_ref_no }}</a></span></td>
      <td>{{ $s->party->party_name }}<span class="sub">{{ $s->party->party_type }}</span></td>
      <td class="num"><b>{{ fmt_mw($s->capacity_mw) }}</b><span class="sub">of {{ fmt_mw($s->lease->leased_capacity_mw) }} leased</span></td>
      <td>{{ fmt_date($s->start_date) }}<span class="sub">to {{ fmt_date($s->end_date) }}</span></td>
      <td class="num">{{ inr($s->rent_amount) }}<span class="sub">{{ $s->rent_frequency }}</span></td>
      <td>{{ chip($s->lender_noc_obtained) }}</td>
      <td>{{ chip($s->status) }}</td>
      <td class="act"><x-row-actions module="sublease" :label="$s->sub_lease_no" :history="route('history', ['sublease', $s->id])" :edit="route('subleases.edit', $s)" :delete="route('subleases.destroy', $s)" /></td>
    </tr>
  @empty
    <tr><td colspan="9" class="empty">{{ $emptyText ?? 'No sub-leases yet.' }}</td></tr>
  @endforelse
  </tbody>
</table></div>
