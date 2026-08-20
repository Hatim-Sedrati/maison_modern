@extends('layouts.storefront')

@section('title', 'Session expired')
@section('robots', 'noindex, follow')

@section('content')
    <x-empty-state
        title="Your session expired"
        text="Please refresh the page and try again."
        :href="url()->previous() !== url()->current() ? url()->previous() : route('home')"
        action="Go back"
    />
@endsection
