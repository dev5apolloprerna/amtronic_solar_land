<form action="{{ route('ownership.finalise', $version) }}" method="POST" novalidate>
  @csrf
  <div class="note-b">
    Finalising makes these shares the ownership in force from the effective date.
    @if ($current) Version {{ $current->version_no }} (in force since {{ fmt_date($current->effective_from) }}) will end the day before and be kept as history. @endif
    Unpaid rent instalments due on or after the effective date are re-split by the new shares. Paid instalments keep their original split.
  </div>
  <fieldset>
    <x-field name="effective_from" label="Effective from" type="date" :value="$current ? now() : ($lease?->lease_start_date ?? now())" req
      :hint="$current ? 'Must be after '.fmt_date($current->effective_from).'.' : 'For the first version, use the lease start date so every instalment is covered.'" />
    <x-field name="change_reason" label="Reason for change" :value="null" :req="$version->version_no > 1" placeholder="e.g. Inheritance after death of owner, sale deed no. …" />
  </fieldset>
  <div class="tbl-wrap"><table>
    <thead><tr><th>Landowner</th><th class="num">Share</th></tr></thead>
    <tbody>
      @foreach ($version->shares()->with('party')->get()->sortByDesc('share_percent') as $s)
        <tr><td>{{ $s->party->party_name }}</td><td class="num">{{ fmt_num($s->share_percent, 4) }}%</td></tr>
      @endforeach
      <tr class="tot"><td>Total</td><td class="num">{{ fmt_num($version->shares()->sum('share_percent'), 4) }}%</td></tr>
    </tbody>
  </table></div>
</form>
