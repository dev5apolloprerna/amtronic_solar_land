@extends('layouts.app')
@section('title', 'City Master')
@section('content')
  <x-page-head title="City Master" table="cities" desc="Cities within each state. The city code is the CITY part of the Land Reference Number.">
    <x-export-buttons route="cities.index" module="city" />
    @if (can_do('city', 'add'))<button class="btn pri" type="button" data-drawer="{{ route('cities.create') }}">Add city</button>@endif
  </x-page-head>
  <x-filter-bar placeholder="Search city or code…" :total="$total" :shown="$rows->total()">
    <x-filter-select name="state_id" label="All states" :options="$states" />
  </x-filter-bar>
  <div class="tbl-wrap"><table>
    <thead><tr><th>City</th><th>Code</th><th>State</th><th>Talukas</th><th>Survey nos.</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse ($rows as $r)
      <tr>
        <td>{{ $r->name }}</td><td><span class="ref">{{ $r->code }}</span></td><td>{{ $r->state->name }}</td>
        <td>{{ $r->talukas_count }}</td><td>{{ $r->plots_count }}</td><td>{{ chip($r->status) }}</td>
        <td class="act"><x-row-actions module="city" :label="$r->name" :history="route('history', ['city', $r->id])" :edit="route('cities.edit', $r)" :delete="route('cities.destroy', $r)" /></td>
      </tr>
    @empty
      <tr><td colspan="7" class="empty">{{ $total ? 'No cities match the search or filters.' : 'No cities yet.' }}</td></tr>
    @endforelse
    </tbody>
  </table></div>
  <x-pager :p="$rows" />
@endsection
