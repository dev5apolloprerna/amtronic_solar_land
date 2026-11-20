@extends('layouts.app')
@section('title', $title)
@section('crumb')Reports · <b>{{ $title }}</b>@endsection
@push('styles')<link rel="stylesheet" href="{{ asset('css/modules/reports.css') }}">@endpush
@section('content')
  <a class="lnk back" href="{{ route('reports.index') }}">← All reports</a>
  <x-page-head :title="$title" table="REPORTS" :desc="$desc">
    <x-export-buttons :route="'reports.show'" module="report" :params="['report' => $key]" />
  </x-page-head>
  <x-filter-bar placeholder="Search any column…" :total="$rows->total()">
    @if (in_array('park', $filters))<x-filter-select name="park_id" label="All parks" :options="$opts['park']" />@endif
    @if (in_array('spv', $filters))<x-filter-select name="spv_id" label="All SPVs" :options="$opts['spv']" />@endif
    @if (in_array('land_status', $filters))<x-filter-select name="land_status" label="All land statuses" :options="opts('land_status')" />@endif
    @if (in_array('lease_status', $filters))<x-filter-select name="status" label="All statuses" :options="opts('lease_status')" />@endif
    @if (in_array('sublease_status', $filters))<x-filter-select name="status" label="All statuses" :options="opts('sublease_status')" />@endif
    @if (in_array('schedule_status', $filters))<x-filter-select name="status" label="All statuses" :options="opts('schedule_status')" />@endif
    @if (in_array('date', $filters))
      <label class="meta">{{ $dateLabel }}</label>
      <input type="date" name="from" value="{{ request('from') }}" aria-label="From">
      <input type="date" name="to" value="{{ request('to') }}" aria-label="To">
    @endif
    @if (in_array('asof', $filters))
      <label class="meta">As on</label><input type="date" name="asof" value="{{ request('asof', now()->toDateString()) }}" aria-label="As on">
    @endif
  </x-filter-bar>
  <div class="tbl-wrap"><table>
    <thead><tr>@foreach ($headings as $h)<th>{{ $h }}</th>@endforeach</tr></thead>
    <tbody>
    @forelse ($rows as $r)
      <tr>@foreach ($r as $i => $c)<td class="{{ is_float($c) || is_int($c) ? 'num' : '' }}">{{ is_float($c) ? fmt_num($c, 3) : $c }}</td>@endforeach</tr>
    @empty
      <tr><td colspan="{{ count($headings) }}" class="empty">No rows match the filters.</td></tr>
    @endforelse
    @if ($rows->total() && $sum)
      <tr class="tot">@foreach ($headings as $i => $h)<td class="{{ isset($totals[$i]) ? 'num' : '' }}">{{ $i === 0 ? 'Total (all pages)' : (isset($totals[$i]) ? fmt_num($totals[$i], 3) : '') }}</td>@endforeach</tr>
    @endif
    </tbody>
  </table></div>
  <x-pager :p="$rows" />
@endsection
