@php($u = auth()->user())
<header class="top">
  <button class="menu" type="button" data-act="menu" aria-label="Open menu">☰ Menu</button>
  <div class="crumb">{{ $u->role->name }} · @hasSection('crumb')@yield('crumb')@else<b>@yield('title')</b>@endif</div>
  <div class="who">
    <span class="uname"><b>{{ $u->name }}</b>{{ $u->role->name }} · {{ $u->username }}</span>
    <span class="av" aria-hidden="true">{{ $u->initials() }}</span>
    <form method="POST" action="{{ route('logout') }}" class="inline-form">@csrf<button class="btn sm" type="submit">Log out</button></form>
  </div>
</header>
