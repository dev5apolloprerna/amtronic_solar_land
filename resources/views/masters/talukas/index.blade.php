@extends('layouts.app')
@section('title', 'Taluka Master')
@section('content')
  <x-page-head title="Taluka Master" table="talukas" desc="Talukas within each city. The taluka code is the TALUKA part of the Land Reference Number.">
    <x-export-buttons route="talukas.index" module="taluka" />
    @if (can_do('taluka', 'add'))<button class="btn pri" type="button" data-drawer="{{ route('talukas.create') }}">Add taluka</button>@endif
  </x-page-head>
  <x-filter-bar placeholder="Search taluka or code…" :total="$total" :shown="$rows->total()">
    <x-filter-select name="state_id" label="All states" :options="$states" />
    <x-filter-select name="city_id" label="All cities" :options="$cities" />
  </x-filter-bar>
  <div class="tbl-wrap"><table>
    <thead><tr><th>Taluka</th><th>Code</th><th>City</th><th>State</th><th>Survey nos.</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse ($rows as $r)
      <tr>
        <td>{{ $r->name }}</td><td><span class="ref">{{ $r->code }}</span></td><td>{{ $r->city->name }}</td><td>{{ $r->city->state->name }}</td>
        <td>{{ $r->plots_count }}</td><td>{{ chip($r->status) }}</td>
        <td class="act"><x-row-actions module="taluka" :label="$r->name" :history="route('history', ['taluka', $r->id])" :edit="route('talukas.edit', $r)" :delete="route('talukas.destroy', $r)" /></td>
      </tr>
    @empty
      <tr><td colspan="7" class="empty">{{ $total ? 'No talukas match the search or filters.' : 'No talukas yet.' }}</td></tr>
    @endforelse
    </tbody>
  </table></div>
  <x-pager :p="$rows" />
@endsection
