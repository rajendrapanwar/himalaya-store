@extends('errors.layout')
@section('title', 'Something went wrong')
@section('code', '500')
@section('eyebrow', 'A small landslide')
@section('heading', 'Something went wrong on our side.')
@section('message', 'Our team has been notified. Please try again in a moment — your cart and account are safe.')
@section('actions')
    <a href="{{ url('/') }}" class="button-primary">Back to home <span class="button-arrow" aria-hidden="true">→</span></a>
    <a href="mailto:{{ config('services.support_email') }}" class="button-secondary">Email customer care</a>
@endsection
