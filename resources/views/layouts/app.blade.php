<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') · {{ config('app.name', 'Leased Land') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  @stack('styles')
</head>
<body>
<div class="app" id="app">
  @include('partials.nav')
  <div class="main">
    @include('partials.header')
    <main id="page">
      @yield('content')
    </main>
  </div>
</div>

{{-- right-hand drawer used by every add / edit form --}}
<div class="scrim" id="scrim" hidden></div>
<aside class="drawer" id="drawer" hidden aria-label="Record form">
  <div class="dr-head" id="drHead"></div>
  <div class="dr-body" id="drBody"></div>
  <div class="dr-foot">
    <span class="note">Saving writes a row to the audit trail.</span>
    <div class="actions"><button class="btn" type="button" data-act="close-drawer">Cancel</button><button class="btn pri" type="button" id="saveBtn">Save</button></div>
  </div>
</aside>
<div class="modal-wrap" id="modal" hidden></div>
<div class="toasts" id="toasts"></div>
@include('partials.flash')

<script src="{{ asset('js/vendor/jquery.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
{{-- drawer forms open from several pages, so the form scripts load everywhere; each acts only on its own module's drawer --}}
<script src="{{ asset('js/modules/forms.js') }}"></script>
@stack('scripts')
</body>
</html>
