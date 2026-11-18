@extends('layouts.app')
@section('title', 'Payment Register')
@section('content')
  <x-page-head title="Payment Register" table="rent_payments" desc="Select a landowner and pay any amount up to their remaining share of an instalment. Multiple part payments are supported.">
    <x-export-buttons route="payments.index" module="payment" />
    @if (can_do('payment', 'add'))<button class="btn pri" type="button" data-drawer="{{ route('payments.create') }}">Record payment</button>@endif
  </x-page-head>
  <x-filter-bar placeholder="Search payment no., UTR, landowner or land ref…" :total="$total" :shown="$rows->total()">
    <x-filter-select name="park_id" label="All parks" :options="$parks" />
    <x-filter-select name="party_id" label="All landowners" :options="$owners" />
    <input type="date" name="from" value="{{ request('from') }}" aria-label="Paid from" title="Paid from">
    <input type="date" name="to" value="{{ request('to') }}" aria-label="Paid to" title="Paid to">
  </x-filter-bar>
  @include('payments._table', ['emptyText' => $total ? 'No payments match the search or filters.' : null])
  <x-pager :p="$rows" />
@endsection
