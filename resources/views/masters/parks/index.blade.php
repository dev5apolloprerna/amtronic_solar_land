@extends('layouts.app')
@section('title', 'Park Master')
@section('content')
  <x-page-head title="Park Master" table="PARK_MASTER" desc="Solar parks. Each park sits in one state, district and city / taluka and can hold several SPVs. Choosing a park on a land parcel fills in its location. Installed capacity (MW) caps the MW of active leases in the park.">
    <x-export-buttons route="parks.index" module="park" />
    @if (can_do('park', 'add'))<button class="btn pri" type="button" data-drawer="{{ route('parks.create') }}">Add park</button>@endif
  </x-page-head>
  <x-filter-bar placeholder="Search park…" :total="$total" :shown="$rows->total()" />
  <div class="tbl-wrap"><table>
    <thead><tr><th>Code</th><th>Park name</th><th>Location</th><th>Area (ac)</th><th>Capacity (MW)</th><th>SPVs</th><th>Parcels</th><th></th></tr></thead>
    <tbody>
    @forelse ($rows as $p)
      @php($leased = $cap->parkLeasedToday($p))
      <tr>
        <td><span class="ref">{{ $p->park_code }}</span></td>
        <td>{{ $p->park_name }}</td>
        <td>{{ $p->city->city_name }}, {{ $p->district->district_name }}<span class="sub">{{ $p->state->state_name }}</span></td>
        <td><span class="num">{{ fmt_num($p->total_area_acres) }}</span></td>
        <td><span class="num">{{ fmt_num($p->installed_capacity_mw, 3) }}</span><span class="sub">{{ fmt_num($leased, 3) }} leased · {{ fmt_num(max(0, (float) $p->installed_capacity_mw - $leased), 3) }} free</span></td>
        <td>{{ $p->spvs->pluck('spv_code')->implode(', ') ?: 'None' }}</td>
        <td>{{ $p->lands_count }}</td>
        <td class="act"><x-row-actions module="park" :history="route('history', ['park', $p->id])" :edit="route('parks.edit', $p)" :delete="route('parks.destroy', $p)" :label="$p->park_code" /></td>
      </tr>
    @empty
      <tr><td colspan="8" class="empty">{{ $total ? 'No records match the search or filters.' : 'No park records yet. Use “Add park” to create the first one.' }}</td></tr>
    @endforelse
    </tbody>
  </table></div>
  <x-pager :p="$rows" />
@endsection
