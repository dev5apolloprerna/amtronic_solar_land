@extends('layouts.app')
@section('title', $land?->land_ref_no ?? 'New land parcel')
@section('crumb')Land Master · <b>{{ $land?->land_ref_no ?? 'New parcel' }}</b> · {{ config("solar.land_tabs.$tab.0") }}@endsection
@push('styles')<link rel="stylesheet" href="{{ asset('css/modules/lands.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/modules/lands.js') }}"></script>@endpush
@section('content')
  <a class="lnk back" href="{{ route('lands.index') }}">← All land parcels</a>

  @if (! $land)
    <section class="lhead"><div class="lh-top"><div><span class="tbl-name">LAND_MASTER</span><h1 class="new">New land parcel</h1>
      <p>Fill in the Land Master details and save. Survey / plot numbers, farmers, lease, rent schedule, payments, sublease, compliance and documents open as tabs once the parcel is saved.</p></div></div></section>
  @else
    @php
      $L = $header['lease'];
      $next = $header['next'];
      $sv = $land->surveys;
      $f = $land->farmers;
    @endphp
    <section class="lhead">
      <div class="lh-top">
        <div><span class="tbl-name">LAND_MASTER · {{ $land->code }}</span><h1>{{ $land->land_ref_no }}</h1>
          <p>@if ($sv->isNotEmpty())Survey {{ $sv->pluck('survey_plot_no')->implode(', ') }} · {{ $sv->pluck('village')->unique()->implode(', ') }} · @else No survey numbers yet · @endif{{ $land->city->city_name }}, {{ $land->district->district_name }}, {{ $land->state->state_name }}</p></div>
        <div class="lh-chips">{{ chip($land->land_status) }}{{ chip('Title DD: '.$land->title_dd_status, config('solar.tones.'.$land->title_dd_status)) }}{{ chip('NA: '.$land->na_status, config('solar.tones.'.$land->na_status)) }}</div>
      </div>
      <dl class="kv">
        <div><dt>Park · SPV</dt><dd>{{ $land->park?->park_code ?? 'NOP' }} · {{ $land->spv->spv_code }}</dd></div>
        <div><dt>Farmers</dt><dd>{{ $f->isNotEmpty() ? $f->first()->owner_name.($f->count() > 1 ? ' +'.($f->count() - 1).' more' : '') : 'None yet' }}</dd></div>
        <div><dt>Area under lease</dt><dd>{{ fmt_num($land->area_under_lease_acres) }} ac</dd></div>
        <div><dt>Rate / increment</dt><dd>{{ inr($land->rate_per_acre) }} · +{{ fmt_num($land->increment_pct) }}% every {{ $land->increment_after_yrs }} yrs</dd></div>
        <div><dt>Active lease</dt><dd>{{ $L ? $L->code.' · to '.fmt_date($L->lease_end_date) : 'None' }}</dd></div>
        <div><dt>Subleased</dt><dd>@if ($L){{ fmt_num($header['areaUsed']) }} of {{ fmt_num($L->leased_area_acres) }} ac<span class="sub">{{ fmt_num($header['mwToday'], 3) }} of {{ fmt_num($L->leased_capacity_mw, 3) }} MW today</span>@else — @endif</dd></div>
        <div><dt>Next rent due</dt><dd>@if ($next){{ fmt_date($next->due_date) }} {{ chip($next->status()) }}@else — @endif</dd></div>
        <div><dt>Connectivity</dt><dd>{{ $land->connectivity_mw ? fmt_num($land->connectivity_mw, 3).' MW · '.$land->substation : '—' }}</dd></div>
      </dl>
    </section>
  @endif

  <div class="ltabs"><div class="tabs" role="tablist">
    @foreach ($landNav['tabs'] as $t => [$tl])
      <a role="tab" aria-selected="{{ $tab === $t ? 'true' : 'false' }}" class="{{ $tab === $t ? 'on' : '' }}" href="{{ $land ? route('lands.show', [$land, 'tab' => $t]) : route('lands.create') }}">{{ $tl }}@if ($t !== 'land' && $land)<i>{{ $landNav['counts'][$t] ?? 0 }}</i>@endif</a>
    @endforeach
    @unless ($land)<span class="lockmsg">Other tabs appear after saving</span>@endunless
  </div></div>

  @if ($tab === 'land')
    @include('lands.tabs.land')
  @elseif ($tab === 'grid')
    @include('lands.tabs.grid')
  @else
    @include('lands.tabs.'.$tab)
  @endif
@endsection
