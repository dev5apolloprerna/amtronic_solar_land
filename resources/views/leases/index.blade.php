@extends('layouts.app')
@section('title', 'Lease Master')
@section('content')
  <x-page-head title="Lease Master" table="leases" desc="Lease agreements with landowners, one active lease per survey number. Each lease carries its contracted MW; sub-leases are carved out of it. Saving a lease generates its rent schedule with escalation and TDS.">
    <x-export-buttons route="leases.index" module="lease" />
    @if (can_do('lease', 'add'))<button class="btn pri" type="button" data-drawer="{{ route('leases.create') }}">Add lease</button>@endif
  </x-page-head>
  <x-filter-bar placeholder="Search lease no., agreement or land ref…" :total="$total" :shown="$rows->total()">
    <x-filter-select name="park_id" label="All parks" :options="$parks" />
    <x-filter-select name="spv_id" label="All SPVs" :options="$spvs" />
    <x-filter-select name="status" label="All statuses" :options="opts('lease_status')" />
    <input type="date" name="from" value="{{ request('from') }}" aria-label="Start from" title="Lease start from">
    <input type="date" name="to" value="{{ request('to') }}" aria-label="Start to" title="Lease start to">
  </x-filter-bar>
  @include('leases._table', ['emptyText' => $total ? 'No leases match the search or filters.' : 'No leases yet. Use “Add lease”, or open a survey number and add one there.'])
  <x-pager :p="$rows" />
@endsection
