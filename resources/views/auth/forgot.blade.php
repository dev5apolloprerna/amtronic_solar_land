@extends('layouts.guest')
@section('title', 'Reset password')
@section('content')
  <h1>Reset password</h1>
  <p>Enter your username or registered email. We will send a link to set a new password.</p>
  @if ($errors->any())<div class="err" role="alert">{{ $errors->first() }}</div>@endif
  @if (session('status'))<div class="ok-box">{{ session('status') }}</div>@endif
  <form method="POST" action="{{ route('password.email') }}" novalidate>
    @csrf
    <div class="fld"><label for="fg_user">Username or email</label><input id="fg_user" name="login" autocomplete="username" value="{{ old('login') }}" autofocus></div>
    <button class="btn pri lg-btn" type="submit">Send reset link</button>
  </form>
  <a class="lnk" href="{{ route('login') }}" style="align-self:flex-start">← Back to sign in</a>
@endsection
