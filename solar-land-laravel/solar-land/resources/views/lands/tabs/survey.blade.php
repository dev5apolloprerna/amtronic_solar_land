@include('lands.partials.tabhead', ['table' => 'LAND_SURVEY', 'desc' => 'One land parcel can cover several survey / plot numbers. Add one row per 7/12 extract. The location starts from the park and can be changed for each survey number.',
  'addUrl' => route('surveys.create', $land), 'addLabel' => 'Add survey'])
@php($diff = (int) round($sqmTotal - (float) $land->area_sqm_712))
<div class="note-b"><b>{{ $total }} survey no{{ $total === 1 ? '' : 's' }}</b> · total {{ fmt_num($sqmTotal) }} sq.m ({{ fmt_num($sqmTotal / config('solar.sqm_per_acre')) }} ac) · Land Master 7/12 area {{ fmt_num($land->area_sqm_712) }} sq.m &nbsp;{{ $diff === 0 ? chip('Matches', 'ok') : chip(($diff > 0 ? '+' : '').fmt_num($diff).' sq.m difference', 'warn') }}</div>
@include('lands.partials.bar', ['what' => 'survey'])
@php($docs = $land->documents()->where('linked_entity_type', 'Survey')->get()->countBy('linked_entity_id'))
<div class="tbl-wrap"><table>
  <thead><tr><th>Survey / plot no.</th><th>Village</th><th>City / taluka</th><th>Area (sq.m)</th><th>Area (ac)</th><th>7/12 docs</th><th></th></tr></thead>
  <tbody>
  @forelse ($rows as $r)
    <tr>
      <td><span class="ref">{{ $r->survey_plot_no }}</span></td>
      <td>{{ $r->village }}</td>
      <td>{{ $r->city->city_name }}<span class="sub">{{ $r->district->district_name }}, {{ $r->state->state_name }}</span></td>
      <td><span class="num">{{ fmt_num($r->area_sqm) }}</span></td>
      <td><span class="num">{{ fmt_num($r->area_acres) }}</span></td>
      <td>{{ $docs[$r->id] ?? 0 }}</td>
      <td class="act">
        @if (can_do('doc', 'add'))<button class="lnk" type="button" data-drawer="{{ route('documents.create', [$land, 'type' => 'Survey', 'id' => $r->id, 'doc_type' => '7/12 extract', 'title' => '7/12 extract survey '.$r->survey_plot_no]) }}">Upload 7/12</button>@endif
        @include('lands.partials.actions', ['module' => 'survey', 'hist' => $r->id, 'edit' => route('surveys.edit', $r), 'delete' => route('surveys.destroy', $r), 'label' => 'survey '.$r->survey_plot_no])
      </td>
    </tr>
  @empty
    <tr><td colspan="7" class="empty">{{ $total ? 'No records match the search or filters.' : 'No survey records yet'.(can_do('survey', 'add') ? '. Use “Add survey” to create the first one.' : '.') }}</td></tr>
  @endforelse
  </tbody>
</table></div>
