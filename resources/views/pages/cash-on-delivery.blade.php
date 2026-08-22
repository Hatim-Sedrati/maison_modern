@extends('layouts.storefront')

@section('title', 'Cash on Delivery')
@section('meta_description', 'Maison Modern accepts Cash on Delivery. You pay when you receive your order. No online payment is required.')
@section('canonical', route('pages.cash-on-delivery'))

@section('content')
    <x-info-page
        title="Cash on Delivery"
        intro="You pay when your order arrives. No online payment is required."
        :crumbs="[
            ['label' => 'Home', 'href' => route('home')],
            ['label' => 'Cash on Delivery'],
        ]"
    >
        <x-info-section title="How you pay">
            <p>Maison Modern currently accepts Cash on Delivery only. You pay the amount due when you receive your order.</p>
            <p>There is no card payment, and you do not need to pay online to place an order.</p>
        </x-info-section>

        <x-info-section title="What you will pay">
            <p>Item prices, quantities, delivery, and the order total are calculated on our side. You will see them in your cart, again at checkout, and on your order confirmation. The confirmed total is the amount to prepare for delivery.</p>
        </x-info-section>

        <x-info-section title="Placing an order">
            <p>You can browse, add to cart, and checkout without creating an account.</p>
            <p>After checkout, you will see your order number. We contact you by phone to confirm the order and the delivery details. Please use a number we can reach you on.</p>
        </x-info-section>

        <x-info-section title="Delivery">
            <p>Maison Modern delivers to all of Morocco. Expected delivery time is 3–7 days. Read more on the <a href="{{ route('pages.delivery') }}" class="underline underline-offset-4 text-charcoal">Delivery</a> page.</p>
        </x-info-section>

        <x-info-section title="Need help?">
            <p>Questions about an order or payment can be sent through <a href="{{ route('pages.contact') }}" class="underline underline-offset-4 text-charcoal">Contact</a>.</p>
        </x-info-section>
    </x-info-page>
@endsection
