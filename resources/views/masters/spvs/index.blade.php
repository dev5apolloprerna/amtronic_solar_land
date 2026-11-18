@extends('layouts.app')
@section('title', 'SPV Master')
@section('content')
  <x-page-head title="SPV Master" table="spvs" desc="Special purpose vehicles. Each SPV belongs to one park and one owning company. Users only see records of the SPVs granted to them.">
    <x-export-buttons route="spvs.index" module="spv" />
    @if (can_do('spv', 'add'))<button class="btn pri" type="button" data-drawer="{{ route('spvs.create') }}">Add SPV</button>@endif
  </x-page-head>
  <x-filter-bar placeholder="Search SPV name or code…" :total="$total" :shown="$rows->total()">
    <x-filter-select name="park_id" label="All parks" :options="$parks" />
    <x-filter-select name="project_status" label="All project statuses" :options="opts('project_status')" />
  </x-filter-bar>
  <div class="tbl-wrap"><table>
    <thead><tr><th>Code</th><th>SPV name</th><th>Park</th><th>Owning company</th><th>Technology</th><th>MWp</th><th>Leased MW</th><th>Survey nos.</th><th>Project</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse ($rows as $r)
      <tr>
        <td><span class="ref">{{ $r->spv_code }}</span></td>
        <td>{{ $r->spv_name }}<span class="sub mono">{{ $r->cin }}</span></td>
        <td>{{ $r->park->park_code }} · {{ $r->park->park_name }}<span class="sub">{{ $r->park->city->name }}, {{ $r->park->state->name }}</span></td>
        <td>{{ $r->party?->party_name ?? '—' }}</td>
        <td>{{ $r->technology }}</td>
        <td class="num">{{ fmt_num($r->capacity_mwp, 3) }}</td>
        <td class="num">{{ fmt_num($r->leased_mw ?? 0, 3) }}</td>
        <td>{{ $r->plots_count }}</td>
        <td>{{ chip($r->project_status) }}</td>
        <td>{{ chip($r->status) }}</td>
        <td class="act"><x-row-actions module="spv" :label="$r->spv_code" :history="route('history', ['spv', $r->id])" :edit="route('spvs.edit', $r)" :delete="route('spvs.destroy', $r)" /></td>
      </tr>
    @empty
      <tr><td colspan="11" class="empty">{{ $total ? 'No SPVs match the search or filters.' : 'No SPVs yet.' }}</td></tr>
    @endforelse
    </tbody>
  </table></div>
  <x-pager :p="$rows" />
@endsection
