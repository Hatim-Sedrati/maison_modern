@extends('layouts.storefront')

@section('title', 'Checkout')
@section('robots', 'noindex, nofollow')
@section('canonical', route('checkout.index'))

@section('content')
    <div class="page-shell py-10 md:py-16">
        <header class="mb-10 md:mb-12">
            <p class="text-[11px] uppercase tracking-[0.22em] text-muted">Guest checkout</p>
            <h1 class="mt-2 section-title">Checkout</h1>
            <p class="mt-4 max-w-lg text-sm leading-relaxed text-charcoal-light">
                No account needed. Payment is Cash on Delivery — you pay when your order arrives.
            </p>
        </header>
        <livewire:checkout.checkout-form />
    </div>
@endsection
