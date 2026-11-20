@extends('layouts.app')
@section('title', 'Land Master')
@push('styles')<link rel="stylesheet" href="{{ asset('css/modules/lands.css') }}">@endpush
@section('content')
  <x-page-head title="Land Master" table="LAND_MASTER" desc="Click a land reference number to open the parcel. Its survey numbers, farmers, lease, rent schedule, payments, sublease, compliance and documents are managed inside the parcel.">
    <x-export-buttons route="lands.index" module="land" />
    @if (can_do('land', 'add'))<a class="btn pri" href="{{ route('lands.create') }}">Add land</a>@endif
  </x-page-head>
  <x-filter-bar placeholder="Search land master…" :total="$total" :shown="$rows->count()" />
  <div class="tbl-wrap"><table>
    <thead><tr><th>Land ref no.</th><th>Park · SPV</th><th>Survey nos.</th><th>Farmers</th><th>Lease area (ac)</th><th>Rate / acre</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse ($rows as $l)
      <tr>
        <td><a class="reflink" href="{{ route('lands.show', $l) }}">{{ $l->land_ref_no }}</a></td>
        <td>{{ $l->park?->park_code ?? 'NOP' }} · {{ $l->spv->spv_code }}<span class="sub">{{ $l->city->city_name }}, {{ $l->state->state_code }}</span></td>
        <td>@if ($l->surveys->isNotEmpty()){{ $l->surveys->pluck('survey_plot_no')->implode(', ') }}<span class="sub">{{ $l->surveys->pluck('village')->unique()->implode(', ') }}</span>@else<span class="hint">None yet</span>@endif</td>
        <td>@if ($l->farmers->isNotEmpty()){{ $l->farmers->first()->owner_name }}@if ($l->farmers->count() > 1)<span class="sub">+{{ $l->farmers->count() - 1 }} more</span>@endif @else<span class="hint">None yet</span>@endif</td>
        <td><span class="num">{{ fmt_num($l->area_under_lease_acres) }}</span></td>
        <td><span class="num">{{ inr($l->rate_per_acre) }}</span><span class="sub">+{{ fmt_num($l->increment_pct) }}% every {{ $l->increment_after_yrs }} yrs</span></td>
        <td>{{ chip($l->land_status) }}</td>
        <td class="act">
          <button class="lnk" type="button" data-history="{{ route('history', ['land', $l->id]) }}">History</button>
          @if (can_do('land', 'edit'))<a class="lnk" href="{{ route('lands.show', [$l, 'edit' => 1]) }}">Edit</a>@else<a class="lnk" href="{{ route('lands.show', $l) }}">Open</a>@endif
          @if (can_do('land', 'delete'))<button class="lnk red" type="button" data-delete-url="{{ route('lands.destroy', $l) }}" data-label="{{ $l->land_ref_no }}">Delete</button>@endif
        </td>
      </tr>
    @empty
      <tr><td colspan="8" class="empty">{{ $total ? 'No records match the search or filters.' : 'No land master records yet. Use “Add land” to create the first one.' }}</td></tr>
    @endforelse
    </tbody>
  </table></div>
@endsection
