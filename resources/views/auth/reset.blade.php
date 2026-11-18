@extends('layouts.guest')
@section('title', 'Set a new password')
@section('content')
  <h1>Set a new password</h1>
  <p>At least 8 characters, with upper and lower case letters and a number.</p>
  @if ($errors->any())<div class="err" role="alert">{{ $errors->first() }}</div>@endif
  <form method="POST" action="{{ route('password.update') }}" novalidate>
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="fld"><label for="rs_email">Email</label><input id="rs_email" name="email" type="email" value="{{ old('email', $email) }}"></div>
    <div class="fld"><label for="rs_pw">New password</label><input id="rs_pw" name="password" type="password" autocomplete="new-password"></div>
    <div class="fld"><label for="rs_pw2">Confirm new password</label><input id="rs_pw2" name="password_confirmation" type="password" autocomplete="new-password"></div>
    <button class="btn pri lg-btn" type="submit">Change password</button>
  </form>
@endsection
