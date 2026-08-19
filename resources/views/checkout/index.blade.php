@extends('layouts.storefront')

@section('title', 'Checkout')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 md:px-6 md:py-14">
        <h1 class="section-title mb-10">Checkout</h1>
        <livewire:checkout.checkout-form />
    </div>
@endsection
