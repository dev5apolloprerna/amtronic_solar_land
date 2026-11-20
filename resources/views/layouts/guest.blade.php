<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Sign in') · {{ config('app.name', 'Leased Land') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
<section class="login">
  <div class="lg-main">
    <div class="brand-lg"><div class="logo">LOGO</div><div><b>Leased Land</b><small>Data management system</small></div></div>
    <div class="lg-card">
      @yield('content')
    </div>
    <p class="lg-foot">LAND · LEASE · SUBLEASE · CAPACITY</p>
  </div>
</section>
</body>
</html>
