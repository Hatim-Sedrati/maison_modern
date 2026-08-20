@extends('layouts.storefront')

@section('title', 'Something went wrong')
@section('robots', 'noindex, follow')

@section('content')
    <x-empty-state
        title="Something went wrong"
        text="We could not complete that request. Please try again in a moment."
        :href="route('home')"
        action="Return Home"
    />
@endsection
