@extends('layouts.storefront')

@section('title', 'Too many requests')
@section('robots', 'noindex, follow')

@section('content')
    <x-empty-state
        title="Please wait a moment"
        text="Too many requests were made. Try again shortly."
        :href="route('home')"
        action="Return Home"
    />
@endsection
