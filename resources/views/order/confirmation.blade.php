@extends('layouts.storefront')

@section('title', 'Order confirmed')

@section('content')
    <div class="mx-auto max-w-xl px-6 py-16 text-center md:py-24">
        <p class="text-xs uppercase tracking-[0.3em] text-muted">Maison Modern</p>
        <h1 class="mt-4 section-title">Order confirmed</h1>
        <p class="mt-6 text-sm leading-relaxed text-charcoal-light">
            Thank you for shopping with Maison Modern. Your order has been received successfully.
        </p>

        <div class="mt-10 border border-ivory-dark bg-white/50 px-6 py-8 text-left">
            <p class="text-xs uppercase tracking-widest text-muted">Order number</p>
            <p class="mt-1 text-lg tracking-wide">#{{ $order->order_number }}</p>

            <p class="mt-6 text-xs uppercase tracking-widest text-muted">Payment</p>
            <p class="mt-1 text-sm">{{ $order->payment_method->getLabel() }}</p>

            <p class="mt-6 text-xs uppercase tracking-widest text-muted">Total</p>
            <p class="mt-1 text-sm">{{ \App\Support\Money::format($order->total) }}</p>
        </div>

        <p class="mt-8 text-sm leading-relaxed text-muted">
            We will contact you shortly to confirm your order. Payment is made in cash when you receive it.
        </p>

        <a href="{{ route('shop.index') }}" class="btn-primary mt-10">Continue shopping</a>
    </div>
@endsection
