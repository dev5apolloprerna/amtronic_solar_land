@extends('layouts.app')
@section('title', 'Compliance Tracker')
@section('content')
  <x-page-head title="Compliance Tracker" table="compliance_records" desc="One record per survey number: mortgage and CERSAI registration, custody of original documents, lender NOC and open pre-disbursement conditions.">
    <x-export-buttons route="compliance.index" module="compliance" />
    @if (can_do('compliance', 'add'))<button class="btn pri" type="button" data-drawer="{{ route('compliance.create') }}">Add compliance record</button>@endif
  </x-page-head>
  <x-filter-bar placeholder="Search land ref, lender or CERSAI no.…" :total="$total" :shown="$rows->total()">
    <x-filter-select name="mortgage_created" label="Mortgage: any" :options="['Yes' => 'Mortgaged', 'No' => 'Not mortgaged']" />
    <x-filter-select name="escalation_level" label="All escalation levels" :options="opts('escalation_level')" />
    <x-filter-select name="open" label="All records" :options="['1' => 'With open PDC items']" />
  </x-filter-bar>
  @include('compliance._table', ['emptyText' => $total ? 'No records match the search or filters.' : null])
  <x-pager :p="$rows" />
@endsection
