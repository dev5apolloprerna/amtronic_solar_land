@extends('layouts.app')
@section('title', 'District Master')
@section('content')
  <x-page-head title="District Master" table="DISTRICT_MASTER" desc="Districts within each state. Land, park and lessor address dropdowns only list the districts of the chosen state.">
    <x-export-buttons route="districts.index" module="district" />
    @if (can_do('district', 'add'))<button class="btn pri" type="button" data-drawer="{{ route('districts.create') }}">Add district</button>@endif
  </x-page-head>
  <x-filter-bar placeholder="Search district…" :total="$total" :shown="$rows->total()">
    <x-filter-select name="state" label="All states" :options="$states" />
  </x-filter-bar>
  <div class="tbl-wrap"><table>
    <thead><tr><th>District</th><th>State</th><th>Cities / talukas</th><th>Land parcels</th><th></th></tr></thead>
    <tbody>
    @forelse ($rows as $r)
      <tr>
        <td>{{ $r->district_name }}</td><td>{{ $r->state->state_name }}</td><td>{{ $r->cities_count }}</td><td>{{ $r->lands_count }}</td>
        <td class="act"><x-row-actions module="district" :history="route('history', ['district', $r->id])" :edit="route('districts.edit', $r)" :delete="route('districts.destroy', $r)" :label="$r->district_name" /></td>
      </tr>
    @empty
      <tr><td colspan="5" class="empty">{{ $total ? 'No records match the search or filters.' : 'No district records yet. Use “Add district” to create the first one.' }}</td></tr>
    @endforelse
    </tbody>
  </table></div>
  <x-pager :p="$rows" />
@endsection
