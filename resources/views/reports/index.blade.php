@extends('layouts.app')
@section('title', 'Reports')
@push('styles')<link rel="stylesheet" href="{{ asset('css/modules/reports.css') }}">@endpush
@section('content')
  <x-page-head title="Reports" desc="Every report supports date filters, search, pagination and Excel / PDF export, and is limited to the SPVs you have access to." />
  <div class="rep-grid">
    @foreach ($catalogue as $key => [$title, $desc])
      <a class="rep-card" href="{{ route('reports.show', $key) }}"><b>{{ $title }}</b><span>{{ $desc }}</span></a>
    @endforeach
  </div>
@endsection
