@extends('errors.layout')
@section('title', 'Access denied')
@section('code', '403')
@section('eyebrow', 'Private trail')
@section('heading', 'You don’t have access to this page.')
@section('message', 'If you believe this is a mistake, sign in with the right account or contact our team.')
@section('actions')
    <a href="{{ url('/') }}" class="button-primary">Back to home <span class="button-arrow" aria-hidden="true">→</span></a>
    <a href="{{ url('/contact') }}" class="button-secondary">Contact us</a>
@endsection
