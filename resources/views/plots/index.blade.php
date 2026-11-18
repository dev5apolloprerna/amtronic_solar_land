@extends('layouts.app')
@section('title', 'Plots / Survey Numbers')
@push('styles')<link rel="stylesheet" href="{{ asset('css/modules/plots.css') }}">@endpush
@section('content')
  <x-page-head title="Plot / Survey Number Master" table="plots" desc="One row per survey number. Click a Land Reference Number to open it: landowners, lease, rent, payments, sub-leases, compliance and documents are managed inside.">
    <x-export-buttons route="plots.index" module="plot" />
    @if (can_do('plot', 'add'))<a class="btn pri" href="{{ route('plots.create') }}">Add survey number</a>@endif
  </x-page-head>
  <x-filter-bar placeholder="Search land ref, survey no. or village…" :total="$total" :shown="$rows->total()">
    <x-filter-select name="park_id" label="All parks" :options="$parks" />
    <x-filter-select name="spv_id" label="All SPVs" :options="$spvs" />
    <x-filter-select name="land_status" label="All statuses" :options="opts('land_status')" />
  </x-filter-bar>
  <div class="tbl-wrap"><table>
    <thead><tr><th>Land ref no.</th><th>Park · SPV</th><th>Survey no.</th><th>Landowners</th><th>Area</th><th>Rate / acre</th><th>Active lease</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse ($rows as $r)
      @php($own = $r->currentOwnership)
      <tr>
        <td><a class="reflink" href="{{ route('plots.show', $r) }}">{{ $r->land_ref_no }}</a></td>
        <td>{{ $r->park->park_code }} · {{ $r->spv->spv_code }}<span class="sub">{{ $r->taluka->name }}, {{ $r->city->name }}, {{ $r->state->code }}</span></td>
        <td>{{ $r->surveyLabel() }}<span class="sub">{{ $r->village }}</span></td>
        <td>
          @if ($own && $own->shares->count())
            {{ $own->shares->first()->party->party_name }}@if ($own->shares->count() > 1)<span class="sub">+{{ $own->shares->count() - 1 }} more</span>@endif
          @else<span class="hint">Not finalised</span>@endif
        </td>
        <td class="num">{{ fmt_num($r->area_acres, 4) }} ac<span class="sub">{{ fmt_num($r->area_under_lease_acres, 4) }} ac under lease</span></td>
        <td class="num">{{ inr($r->rate_per_acre) }}<span class="sub">+{{ fmt_num($r->increment_pct) }}% every {{ $r->increment_after_yrs }} yrs</span></td>
        <td>{!! $r->activeLease ? '<span class="ref">'.e($r->activeLease->lease_no).'</span><span class="sub">'.fmt_mw($r->activeLease->leased_capacity_mw).'</span>' : '—' !!}</td>
        <td>{{ chip($r->land_status) }}</td>
        <td class="act">
          <button class="lnk" type="button" data-history="{{ route('history', ['plot', $r->id]) }}">History</button>
          <a class="lnk" href="{{ route('plots.show', can_do('plot', 'edit') ? [$r, 'edit' => 1] : $r) }}">{{ can_do('plot', 'edit') ? 'Edit' : 'Open' }}</a>
          @if (can_do('plot', 'delete'))<button class="lnk red" type="button" data-delete-url="{{ route('plots.destroy', $r) }}" data-label="{{ $r->land_ref_no }}">Delete</button>@endif
        </td>
      </tr>
    @empty
      <tr><td colspan="9" class="empty">{{ $total ? 'No survey numbers match the search or filters.' : 'No survey numbers yet. Use “Add survey number” to create the first one.' }}</td></tr>
    @endforelse
    </tbody>
  </table></div>
  <x-pager :p="$rows" />
@endsection
