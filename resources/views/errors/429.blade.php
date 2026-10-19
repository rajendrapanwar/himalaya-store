@extends('errors.layout')
@section('title', 'Too many requests')
@section('code', '429')
@section('eyebrow', 'Catch your breath')
@section('heading', 'That was a lot of requests in a short time.')
@section('message', 'Please wait a minute before trying again. This limit keeps every customer’s account and checkout safe.')
@section('actions')
    <a href="{{ url('/') }}" class="button-primary">Back to home <span class="button-arrow" aria-hidden="true">→</span></a>
@endsection
