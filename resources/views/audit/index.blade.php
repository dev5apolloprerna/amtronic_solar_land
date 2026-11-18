@extends('layouts.app')
@section('title', 'Audit Trail')
@section('content')
  <x-page-head title="Audit trail" table="audit_logs" desc="Every create, edit, delete, export and sign-in is written here with the user, time, IP address and the old and new value of each changed field. Entries cannot be edited or deleted.">
    <x-export-buttons route="audit.index" module="audit" />
  </x-page-head>
  <x-filter-bar placeholder="Search record, field or value…">
    <x-filter-select name="module" label="All tables" :options="$modules" />
    <x-filter-select name="action" label="All actions" :options="array_combine(\App\Http\Controllers\AuditLogController::ACTIONS, \App\Http\Controllers\AuditLogController::ACTIONS)" />
    <x-filter-select name="user" label="All users" :options="$users" />
    <input type="date" name="from" value="{{ request('from') }}" aria-label="From">
    <input type="date" name="to" value="{{ request('to') }}" aria-label="To">
    <span class="meta">{{ $rows->total() }} entries</span>
  </x-filter-bar>
  @include('audit._table')
  <x-pager :p="$rows" />
@endsection
