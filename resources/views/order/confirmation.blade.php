@extends('layouts.storefront')

@section('title', 'Order confirmed')

@section('content')
    <div class="mx-auto max-w-xl px-5 py-16 md:py-24">
        <div class="text-center">
            <p class="text-[11px] uppercase tracking-[0.28em] text-muted">Maison Modern</p>
            <h1 class="mt-4 font-display text-3xl tracking-wide text-ink md:text-4xl">Order confirmed</h1>
            <p class="mt-5 text-sm leading-relaxed text-charcoal-light">
                Thank you. Your order has been received and we will contact you to confirm it.
                Payment is Cash on Delivery — you pay when you receive your order.
            </p>
        </div>

        <div class="mt-10 bg-ivory px-6 py-8">
            <p class="text-[11px] uppercase tracking-[0.16em] text-muted">Order number</p>
            <p class="mt-1 text-lg tracking-wide">#{{ $order->order_number }}</p>

            <p class="mt-6 text-[11px] uppercase tracking-[0.16em] text-muted">Payment</p>
            <p class="mt-1 text-sm">{{ $order->payment_method->getLabel() }}</p>

            <p class="mt-6 text-[11px] uppercase tracking-[0.16em] text-muted">Delivery</p>
            <p class="mt-1 text-sm">{{ $order->customer_name }}</p>
            <p class="text-sm text-charcoal-light">{{ $order->address }}</p>
            <p class="text-sm text-charcoal-light">{{ $order->city }}@if ($order->postal_code), {{ $order->postal_code }}@endif</p>
            @if ($order->phone)
                <p class="mt-1 text-sm text-charcoal-light">{{ $order->phone }}</p>
            @endif

            <p class="mt-6 text-[11px] uppercase tracking-[0.16em] text-muted">Items</p>
            <ul class="mt-3 divide-y divide-sand" role="list">
                @foreach ($order->items as $item)
                    <li class="flex justify-between gap-4 py-3 text-sm">
                        <div>
                            <p>{{ $item->product_name }}</p>
                            <p class="text-xs text-muted">
                                Qty {{ $item->quantity }}
                                @if ($item->selected_color) · {{ $item->selected_color }} @endif
                                @if ($item->selected_size) · {{ $item->selected_size }} @endif
                            </p>
                        </div>
                        <p class="shrink-0">{{ \App\Support\Money::format($item->total) }}</p>
                    </li>
                @endforeach
            </ul>

            <dl class="mt-4 space-y-2 border-t border-sand pt-4 text-sm">
                <div class="flex justify-between">
                    <dt>Subtotal</dt>
                    <dd>{{ \App\Support\Money::format($order->subtotal) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt>Delivery</dt>
                    <dd>{{ \App\Support\Money::isZero($order->delivery_fee) ? 'Free' : \App\Support\Money::format($order->delivery_fee) }}</dd>
                </div>
                <div class="flex justify-between pt-2 text-base">
                    <dt>Total</dt>
                    <dd>{{ \App\Support\Money::format($order->total) }}</dd>
                </div>
            </dl>
        </div>

        @if ($whatsappUrl)
            <div class="mt-8 text-center">
                <a
                    href="{{ $whatsappUrl }}"
                    class="btn-primary w-full"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Confirm your order on WhatsApp
                </a>
                <p class="mt-3 text-xs leading-relaxed text-muted">
                    Opens WhatsApp with your order details so we can confirm it with you.
                </p>
            </div>
        @endif

        <div class="mt-8 text-center">
            <a href="{{ route('shop.index') }}" class="btn-secondary w-full">Continue Shopping</a>
        </div>
    </div>
@endsection
