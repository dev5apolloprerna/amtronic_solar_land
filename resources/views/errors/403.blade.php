@extends('errors.layout')
@section('code', '403')
@section('heading', 'You can’t open this')
@section('message', $exception->getMessage() ?: 'Your role or SPV access does not allow this page.')
