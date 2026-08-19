@extends('layouts.storefront')

@section('title', 'Page not found')

@section('content')
    <x-empty-state
        title="Page not found"
        text="This page is not available. Continue shopping our latest collection."
        :href="route('shop.index')"
        action="Shop now"
    />
@endsection
