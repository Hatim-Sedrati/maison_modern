@extends('layouts.storefront')

@section('title', 'Link expired')
@section('robots', 'noindex, follow')

@section('content')
    <x-empty-state
        title="This page is not available"
        text="The confirmation link is invalid or has expired. If you just placed an order, we still received it."
        :href="route('home')"
        action="Return Home"
    />
@endsection
