@extends('layouts.app')
@section('title', 'State Master')
@section('content')
  <x-page-head title="State Master" table="STATE_MASTER" desc="States used in every location dropdown. The state code is the second part of the land reference number.">
    <x-export-buttons route="states.index" module="state" />
    @if (can_do('state', 'add'))<button class="btn pri" type="button" data-drawer="{{ route('states.create') }}">Add state</button>@endif
  </x-page-head>
  <x-filter-bar placeholder="Search state…" :total="$total" :shown="$rows->total()" />
  <div class="tbl-wrap"><table>
    <thead><tr><th>State</th><th>Code</th><th>Districts</th><th>Land parcels</th><th></th></tr></thead>
    <tbody>
    @forelse ($rows as $r)
      <tr>
        <td>{{ $r->state_name }}</td><td><span class="ref">{{ $r->state_code }}</span></td><td>{{ $r->districts_count }}</td><td>{{ $r->lands_count }}</td>
        <td class="act"><x-row-actions module="state" :history="route('history', ['state', $r->id])" :edit="route('states.edit', $r)" :delete="route('states.destroy', $r)" :label="$r->state_name" /></td>
      </tr>
    @empty
      <tr><td colspan="5" class="empty">{{ $total ? 'No records match the search or filters.' : 'No state records yet. Use “Add state” to create the first one.' }}</td></tr>
    @endforelse
    </tbody>
  </table></div>
  <x-pager :p="$rows" />
@endsection
