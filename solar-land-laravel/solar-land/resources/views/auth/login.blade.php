@extends('layouts.guest')
@section('title', 'Sign in')
@section('content')
  <h1>Sign in</h1>
  @if ($errors->any())<div class="err" role="alert">{{ $errors->first() }}</div>@endif
  @if (session('status'))<div class="ok-box">{{ session('status') }}</div>@endif
  <form method="POST" action="{{ route('login') }}" novalidate>
    @csrf
    <div class="fld"><label for="lg_user">Username or email</label><input id="lg_user" name="username" autocomplete="username" value="{{ old('username') }}" placeholder="e.g. superadmin" autofocus></div>
    <div class="fld"><label for="lg_pass">Password</label>
      <div class="pw"><input id="lg_pass" name="password" type="password" autocomplete="current-password"><button type="button" class="lnk" onclick="const p=document.getElementById('lg_pass');p.type=p.type==='password'?'text':'password';this.textContent=p.type==='password'?'Show':'Hide'">Show</button></div>
    </div>
    <div class="lg-row"><label class="chk"><input type="checkbox" name="remember" value="1"> Keep me signed in</label><a class="lnk" href="{{ route('password.request') }}">Forgot password?</a></div>
    <button class="btn pri lg-btn" type="submit">Sign in</button>
  </form>
@endsection
