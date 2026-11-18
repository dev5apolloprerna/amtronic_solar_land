@extends('layouts.app')
@section('title', 'New survey number')
@section('crumb')Plot / Survey No. · <b>New survey number</b>@endsection
@push('styles')<link rel="stylesheet" href="{{ asset('css/modules/plots.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/modules/plots.js') }}"></script>@endpush
@section('content')
  <a class="lnk back" href="{{ route('plots.index') }}">← All survey numbers</a>
  <section class="lhead"><div class="lh-top"><div><span class="tbl-name">PLOTS</span><h1 class="new">New survey number</h1>
    <p>Fill in the details and save. The Land Reference Number is generated on save. Landowners, lease, rent, sub-leases, compliance and documents open as tabs once it is saved.</p></div></div></section>
  <section class="inline-card" id="inlineHost">
    <div class="inline-bar"><h2>Survey details</h2></div>
    <form id="plotForm" action="{{ route('plots.store') }}" method="POST" data-ajax novalidate
      data-ref-url="{{ route('lookup.land-ref') }}" data-spv-url="{{ route('lookup.spvs') }}">
      @csrf
      <div id="inlineBody">@include('plots._form')</div>
      <div class="inline-foot"><span class="hint" style="flex:1;align-self:center">Saving creates the Land Reference Number and writes to the audit trail.</span>
        <a class="btn" href="{{ route('plots.index') }}">Cancel</a><button class="btn pri" type="submit">Save survey number</button></div>
    </form>
  </section>
@endsection
