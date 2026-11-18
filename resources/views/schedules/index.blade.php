@extends('layouts.app')
@section('title', 'Rent Schedule')
@section('content')
  <x-page-head title="Rent Schedule" table="rent_schedules" desc="Instalment-level totals, paid and remaining balance, generated from each active lease. Use Payment history to see landowner-wise payments for an instalment.">
    <x-export-buttons route="schedules.index" module="schedule" />
  </x-page-head>
  <x-filter-bar placeholder="Search lease no. or land ref…" :total="$total" :shown="$rows->total()">
    <x-filter-select name="lease_id" label="All leases" :options="$leases" />
    <x-filter-select name="park_id" label="All parks" :options="$parks" />
    <x-filter-select name="status" label="All statuses" :options="array_combine(\App\Http\Controllers\RentScheduleController::FILTER_STATUSES, \App\Http\Controllers\RentScheduleController::FILTER_STATUSES)" />
    <input type="date" name="from" value="{{ request('from') }}" aria-label="Due from" title="Due from">
    <input type="date" name="to" value="{{ request('to') }}" aria-label="Due to" title="Due to">
  </x-filter-bar>
  @include('schedules._table', ['emptyText' => $total ? 'No instalments match the search or filters.' : null])
  <x-pager :p="$rows" />
@endsection
