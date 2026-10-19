@extends('errors.layout')
@section('title', 'Page expired')
@section('code', '419')
@section('eyebrow', 'Session timed out')
@section('heading', 'This page has been open for a while.')
@section('message', 'For your security, the form expired. Go back, refresh the page and try again.')
@section('actions')
    <a href="{{ url()->previous() }}" class="button-primary">Go back and retry <span class="button-arrow" aria-hidden="true">→</span></a>
    <a href="{{ url('/') }}" class="button-secondary">Back to home</a>
@endsection
