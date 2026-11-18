@extends('errors.layout')
@section('code', '404')
@section('heading', 'Not found')
@section('message', $exception->getMessage() ?: 'This record does not exist or was deleted.')
