@extends('layouts.app')
@section('title', $plot->land_ref_no)
@section('crumb')Plot / Survey No. · <b>{{ $plot->land_ref_no }}</b> · {{ $tabs[$tab][0] }}@endsection
@push('styles')<link rel="stylesheet" href="{{ asset('css/modules/plots.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/modules/plots.js') }}"></script>@endpush
@section('content')
  @php($own = $plot->currentOwnership)
  <a class="lnk back" href="{{ route('plots.index') }}">← All survey numbers</a>
  <section class="lhead">
    <div class="lh-top">
      <div>
        <span class="tbl-name">PLOTS · #{{ $plot->id }}</span>
        <h1>{{ $plot->land_ref_no }}</h1>
        <p>Survey {{ $plot->surveyLabel() }} · {{ $plot->village }} · {{ $plot->taluka->name }}, {{ $plot->city->name }}, {{ $plot->state->name }}</p>
      </div>
      <div class="lh-chips">{{ chip($plot->land_status) }} {{ chip('Title DD: '.$plot->title_dd_status, config('solar.tones.'.$plot->title_dd_status)) }} {{ chip('NA: '.$plot->na_status, config('solar.tones.'.$plot->na_status)) }}</div>
    </div>
    <dl class="kv">
      <div><dt>Park · SPV</dt><dd>{{ $plot->park->park_code }} · {{ $plot->spv->spv_code }}</dd></div>
      <div><dt>Landowners</dt><dd>
        @if ($own && $own->shares->count()){{ $own->shares->first()->party->party_name }}@if ($own->shares->count() > 1) +{{ $own->shares->count() - 1 }} more @endif
        @else Not finalised @endif
        @if ($plot->draftOwnership) {{ chip('Draft open', 'info') }} @endif
      </dd></div>
      <div><dt>Area</dt><dd>{{ fmt_num($plot->total_area, 4) }} {{ $plot->land_unit }} · {{ fmt_num($plot->area_under_lease_acres, 4) }} ac under lease</dd></div>
      <div><dt>Rate / increment</dt><dd>{{ inr($plot->rate_per_acre) }} · +{{ fmt_num($plot->increment_pct) }}% every {{ $plot->increment_after_yrs }} yrs</dd></div>
      <div><dt>Active lease</dt><dd>{{ $lease ? $lease->lease_no.' · to '.fmt_date($lease->lease_end_date) : 'None' }}</dd></div>
      <div><dt>Capacity</dt><dd>{{ $lease ? fmt_mw($lease->leased_capacity_mw).' leased · '.fmt_mw($subToday).' sub-leased today' : '—' }}</dd></div>
      <div><dt>Next rent due</dt><dd>@if ($next){{ fmt_date($next->due_date) }} {{ chip($next->displayStatus()) }}@else — @endif</dd></div>
      <div><dt>Connectivity</dt><dd>{{ $plot->grid?->connectivity_mw ? fmt_mw($plot->grid->connectivity_mw).' · '.$plot->grid->substation : '—' }}</dd></div>
    </dl>
  </section>

  <div class="ltabs"><div class="tabs" role="tablist">
    @foreach ($tabs as $key => [$label, $mod])
      <a role="tab" aria-selected="{{ $tab === $key ? 'true' : 'false' }}" class="{{ $tab === $key ? 'on' : '' }}" href="{{ route('plots.show', [$plot, 'tab' => $key]) }}"
         style="text-decoration:none;display:flex;gap:6px;align-items:center;padding:11px 14px;color:{{ $tab === $key ? 'var(--ink)' : 'var(--muted)' }};border-bottom:2px solid {{ $tab === $key ? 'var(--accent)' : 'transparent' }};margin-bottom:-1px;font-weight:{{ $tab === $key ? 600 : 500 }}">
        {{ $label }}@if ($key !== 'details')<i>{{ $counts[$key] ?? '' }}</i>@endif
      </a>
    @endforeach
  </div></div>

  @include('plots.tabs.'.$tab)
@endsection
