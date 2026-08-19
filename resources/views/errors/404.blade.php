@extends('layouts.storefront')

@section('title', 'Page not found')

@section('content')
    <x-empty-state
        title="Page not found"
        text="Let's get you back to Maison Modern."
        :href="route('home')"
        action="Return Home"
    />
@endsection
