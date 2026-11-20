@include('lands.partials.tabhead', ['table' => 'LAND_OWNER', 'desc' => 'Every farmer or co-owner named on the 7/12 extracts of this parcel, with KYC and bank details for rent payment.',
  'addUrl' => route('farmers.create', $land), 'addLabel' => 'Add farmer'])
@foreach ($allocations as $a)
  @php($sum = $land->ownershipTotal())
  <div class="panel">
    <h3>Farmer-wise rent allocation · {{ $a['lease']->code }} <span class="sub">{{ $land->land_ref_no }}</span></h3>
    <div class="pb">
      <div class="tiles">
        <div class="tile"><small>Rent / instalment</small><b>{{ inr($a['rent']) }}</b></div>
        <div class="tile"><small>TDS</small><b>{{ inr($a['tds']) }}</b></div>
        <div class="tile"><small>Net payable</small><b>{{ inr($a['net']) }}</b></div>
        <div class="tile"><small>Total owner share</small><b style="color:{{ $a['valid'] ? 'var(--ok)' : 'var(--bad)' }}">{{ fmt_num($sum) }}%</b></div>
      </div>
      @unless ($a['valid'])
        <div class="err">Owner percentages must add up to exactly 100% to allocate rent. Currently {{ fmt_num($sum) }}%. {{ $land->farmers->isNotEmpty() ? 'Edit the percentages in Farmers / Owners.' : 'Add farmers and their ownership shares.' }}</div>
      @endunless
      <div class="tbl-wrap"><table>
        <thead><tr><th>Farmer</th><th>Ownership %</th><th>Gross rent</th><th>TDS</th><th>Net payable</th></tr></thead>
        <tbody>
        @foreach ($a['split'] as $x)
          <tr><td>{{ $x['farmer']->owner_name }}</td><td>{{ fmt_num($x['pct']) }}%</td><td>{{ $a['valid'] ? inr($x['share']) : '—' }}</td><td>{{ $a['valid'] ? inr($x['tds']) : '—' }}</td><td><b>{{ $a['valid'] ? inr($x['net']) : '—' }}</b></td></tr>
        @endforeach
          <tr><td><b>Total</b></td><td><b>{{ fmt_num($sum) }}%</b></td><td><b>{{ $a['valid'] ? inr($a['split']->sum('share')) : '—' }}</b></td><td><b>{{ $a['valid'] ? inr($a['split']->sum('tds')) : '—' }}</b></td><td><b>{{ $a['valid'] ? inr($a['split']->sum('net')) : '—' }}</b></td></tr>
        </tbody>
      </table></div>
      <span class="hint">Distribution preview uses the first scheduled instalment ({{ fmt_date($a['due']) }}) and its TDS. Actual payment recording remains in Payment Register.</span>
    </div>
  </div>
@endforeach
@include('lands.partials.bar', ['what' => 'farmer'])
<div class="tbl-wrap"><table>
  <thead><tr><th>Share (%)</th><th>Farmer name</th><th>PAN</th><th>Aadhaar no.</th><th>Voter ID</th><th>Bank A/c no.</th><th>Bank &amp; branch</th><th></th></tr></thead>
  <tbody>
  @forelse ($rows as $r)
    <tr>
      <td><b>{{ fmt_num($r->ownership_pct) }}%</b></td>
      <td><b>{{ $r->owner_name }}</b><span class="sub mono">{{ $r->code }}</span></td>
      <td><span class="mono">{{ $r->owner_pan ?: '—' }}</span></td>
      <td><span class="mono">{{ $r->aadhaar ?: '—' }}</span></td>
      <td><span class="mono">{{ $r->voter_id ?: '—' }}</span></td>
      <td><span class="mono">{{ $r->maskedBank() }}</span></td>
      <td>{{ $r->bank_branch ?: '—' }}</td>
      <td class="act">@include('lands.partials.actions', ['module' => 'farmer', 'hist' => $r->id, 'edit' => route('farmers.edit', $r), 'delete' => route('farmers.destroy', $r), 'label' => $r->owner_name])</td>
    </tr>
  @empty
    <tr><td colspan="8" class="empty">{{ $total ? 'No records match the search or filters.' : 'No farmer records yet'.(can_do('farmer', 'add') ? '. Use “Add farmer” to create the first one.' : '.') }}</td></tr>
  @endforelse
  </tbody>
</table></div>
