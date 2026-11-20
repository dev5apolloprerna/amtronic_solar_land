@extends('layouts.app')
@section('title', 'City / Taluka Master')
@section('content')
  <x-page-head title="City / Taluka Master" table="CITY_MASTER" desc="Cities, talukas and tehsils within each district.">
    <x-export-buttons route="cities.index" module="city" />
    @if (can_do('city', 'add'))<button class="btn pri" type="button" data-drawer="{{ route('cities.create') }}">Add city / taluka</button>@endif
  </x-page-head>
  <x-filter-bar placeholder="Search city / taluka…" :total="$total" :shown="$rows->total()">
    <x-filter-select name="state" label="All states" :options="$states" />
    <x-filter-select name="district" label="All districts" :options="$districts" />
  </x-filter-bar>
  <div class="tbl-wrap"><table>
    <thead><tr><th>City / taluka</th><th>Type</th><th>District</th><th>State</th><th>Land parcels</th><th></th></tr></thead>
    <tbody>
    @forelse ($rows as $r)
      <tr>
        <td>{{ $r->city_name }}</td><td>{{ chip($r->city_type, 'info') }}</td><td>{{ $r->district->district_name }}</td><td>{{ $r->state->state_name }}</td><td>{{ $r->lands_count }}</td>
        <td class="act"><x-row-actions module="city" :history="route('history', ['city', $r->id])" :edit="route('cities.edit', $r)" :delete="route('cities.destroy', $r)" :label="$r->city_name" /></td>
      </tr>
    @empty
      <tr><td colspan="6" class="empty">{{ $total ? 'No records match the search or filters.' : 'No city / taluka records yet. Use “Add city / taluka” to create the first one.' }}</td></tr>
    @endforelse
    </tbody>
  </table></div>
  <x-pager :p="$rows" />
@endsection
