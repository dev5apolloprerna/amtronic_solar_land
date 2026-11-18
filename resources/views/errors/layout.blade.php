<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('code') · {{ config('app.name', 'Solar Land Lease') }}</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}"><link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
<section class="login"><div class="lg-main"><div class="lg-card">
  <span class="tbl-name">ERROR @yield('code')</span>
  <h1>@yield('heading')</h1>
  <p>@yield('message')</p>
  <a class="btn pri lg-btn" href="{{ url('/') }}" style="text-align:center">Back to the dashboard</a>
</div></div></section>
</body>
</html>
