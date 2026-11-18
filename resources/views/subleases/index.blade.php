@extends('layouts.app')
@section('title', 'Sub-Lease Master')
@section('content')
  <x-page-head title="Sub-Lease Master" table="sub_leases" desc="Electricity capacity carved out of a parent lease. On any day, active sub-leases can never exceed the lease's MW: a 200 MW lease with 100 MW sub-leased shows 100 MW remaining.">
    <x-export-buttons route="subleases.index" module="sublease" />
    @if (can_do('sublease', 'add'))<button class="btn pri" type="button" data-drawer="{{ route('subleases.create') }}">Add sub-lease</button>@endif
  </x-page-head>
  <x-filter-bar placeholder="Search sub-lease, lease, agreement or party…" :total="$total" :shown="$rows->total()">
    <x-filter-select name="park_id" label="All parks" :options="$parks" />
    <x-filter-select name="lease_id" label="All leases" :options="$leases" />
    <x-filter-select name="party_id" label="All sub-lessees" :options="$parties" />
    <x-filter-select name="status" label="All statuses" :options="opts('sublease_status')" />
    <input type="date" name="from" value="{{ request('from') }}" aria-label="Running from" title="Running on or after">
    <input type="date" name="to" value="{{ request('to') }}" aria-label="Running to" title="Running on or before">
  </x-filter-bar>
  @include('subleases._table', ['emptyText' => $total ? 'No sub-leases match the search or filters.' : 'No sub-leases yet. Use “Add sub-lease” on an active lease.'])
  <x-pager :p="$rows" />
@endsection
