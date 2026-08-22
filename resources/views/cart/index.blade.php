@extends('layouts.storefront')

@section('title', 'Cart')
@section('robots', 'noindex, nofollow')
@section('canonical', route('cart.index'))

@section('content')
    <div class="page-shell py-10 md:py-16">
        <h1 class="section-title mb-10 md:mb-12">Cart</h1>
        <livewire:cart.cart-page />
    </div>
@endsection
