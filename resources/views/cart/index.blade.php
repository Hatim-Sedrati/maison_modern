@extends('layouts.storefront')

@section('title', 'Cart')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 md:px-6 md:py-14">
        <h1 class="section-title mb-10">Cart</h1>
        <livewire:cart.cart-page />
    </div>
@endsection
