@extends('layouts.app')
@section('title', 'State Master')
@section('content')
  <x-page-head title="State Master" table="states" desc="States used in every location dropdown. The state code is the STATE part of the Land Reference Number and locks once a survey number uses it.">
    <x-export-buttons route="states.index" module="state" />
    @if (can_do('state', 'add'))<button class="btn pri" type="button" data-drawer="{{ route('states.create') }}">Add state</button>@endif
  </x-page-head>
  <x-filter-bar placeholder="Search state or code…" :total="$total" :shown="$rows->total()">
    <x-filter-select name="status" label="All statuses" :options="opts('status')" />
  </x-filter-bar>
  <div class="tbl-wrap"><table>
    <thead><tr><th>State</th><th>Code</th><th>Cities</th><th>Survey nos.</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse ($rows as $r)
      <tr>
        <td>{{ $r->name }}</td><td><span class="ref">{{ $r->code }}</span></td>
        <td>{{ $r->cities_count }}</td><td>{{ $r->plots_count }}</td><td>{{ chip($r->status) }}</td>
        <td class="act"><x-row-actions module="state" :label="$r->name" :history="route('history', ['state', $r->id])" :edit="route('states.edit', $r)" :delete="route('states.destroy', $r)" /></td>
      </tr>
    @empty
      <tr><td colspan="6" class="empty">{{ $total ? 'No states match the search or filters.' : 'No states yet. Use “Add state” to create the first one.' }}</td></tr>
    @endforelse
    </tbody>
  </table></div>
  <x-pager :p="$rows" />
@endsection
