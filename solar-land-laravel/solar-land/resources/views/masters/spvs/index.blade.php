@extends('layouts.app')
@section('title', 'SPV Master')
@section('content')
  <x-page-head title="SPV Master" table="SPV_MASTER" desc="Select a state first, then choose a park from that state. The SPV code becomes the first part of land reference numbers.">
    <x-export-buttons route="spvs.index" module="spv" />
    @if (can_do('spv', 'add'))<button class="btn pri" type="button" data-drawer="{{ route('spvs.create') }}">Add spv</button>@endif
  </x-page-head>
  <x-filter-bar placeholder="Search spv master…" :total="$total" :shown="$rows->total()" />
  <div class="tbl-wrap"><table>
    <thead><tr><th>Code</th><th>SPV name</th><th>Park</th><th>Technology</th><th>MWp</th><th>Parcels</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse ($rows as $s)
      <tr>
        <td><span class="ref">{{ $s->spv_code }}</span></td>
        <td>{{ $s->spv_name }}</td>
        <td>@if ($s->park){{ $s->park->park_code }} · {{ $s->park->park_name }}<span class="sub">{{ $s->park->city->city_name }}, {{ $s->park->state->state_name }}</span>@else — @endif</td>
        <td>{{ $s->technology }}</td>
        <td><span class="num">{{ fmt_num($s->capacity_mwp, 3) }}</span></td>
        <td>{{ $s->lands_count }}</td>
        <td>{{ chip($s->status) }}</td>
        <td class="act"><x-row-actions module="spv" :history="route('history', ['spv', $s->id])" :edit="route('spvs.edit', $s)" :delete="route('spvs.destroy', $s)" :label="$s->spv_code" /></td>
      </tr>
    @empty
      <tr><td colspan="8" class="empty">{{ $total ? 'No records match the search or filters.' : 'No spv master records yet. Use “Add spv” to create the first one.' }}</td></tr>
    @endforelse
    </tbody>
  </table></div>
  <x-pager :p="$rows" />
@endsection
