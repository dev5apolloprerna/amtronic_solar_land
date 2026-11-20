@extends('errors.layout')
@section('code', '419')
@section('heading', 'Session expired')
@section('message', $exception->getMessage() ?: 'Your session expired. Sign in again and retry.')
