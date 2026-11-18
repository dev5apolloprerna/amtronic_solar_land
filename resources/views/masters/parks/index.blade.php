@extends('layouts.app')
@section('title', 'Park Master')
@section('content')
  <x-page-head title="Park Master" table="parks" desc="Solar parks. The park code is the first part of every Land Reference Number and is locked after saving. Leased MW cannot exceed installed MW.">
    <x-export-buttons route="parks.index" module="park" />
    @if (can_do('park', 'add'))<button class="btn pri" type="button" data-drawer="{{ route('parks.create') }}">Add park</button>@endif
  </x-page-head>
  <x-filter-bar placeholder="Search park name or code…" :total="$total" :shown="$rows->total()">
    <x-filter-select name="status" label="All statuses" :options="opts('status')" />
  </x-filter-bar>
  <div class="tbl-wrap"><table>
    <thead><tr><th>Code</th><th>Park name</th><th>Location</th><th>Land area</th><th>Installed</th><th>Leased (active)</th><th>SPVs</th><th>Survey nos.</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse ($rows as $r)
      <tr>
        <td><span class="ref">{{ $r->park_code }}</span></td>
        <td>{{ $r->park_name }}</td>
        <td>{{ $r->taluka->name }}, {{ $r->city->name }}<span class="sub">{{ $r->state->name }}</span></td>
        <td class="num">{{ fmt_num($r->total_land_area, 4) }} {{ $r->land_unit }}<span class="sub">{{ fmt_num($r->total_land_acres) }} ac</span></td>
        <td class="num"><b>{{ fmt_mw($r->installed_capacity_mw) }}</b></td>
        <td class="num">{{ fmt_mw($r->leased_mw ?? 0) }}<span class="sub">{{ fmt_mw((float) $r->installed_capacity_mw - (float) $r->leased_mw) }} unleased</span></td>
        <td>{{ $r->spvs->pluck('spv_code')->implode(', ') ?: 'None' }}</td>
        <td>{{ $r->plots_count }}</td>
        <td>{{ chip($r->status) }}</td>
        <td class="act"><x-row-actions module="park" :label="$r->park_code" :history="route('history', ['park', $r->id])" :edit="route('parks.edit', $r)" :delete="route('parks.destroy', $r)" /></td>
      </tr>
    @empty
      <tr><td colspan="10" class="empty">{{ $total ? 'No parks match the search or filters.' : 'No parks yet. Use “Add park” to create the first one.' }}</td></tr>
    @endforelse
    </tbody>
  </table></div>
  <x-pager :p="$rows" />
@endsection
