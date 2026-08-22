@extends('layouts.storefront')

@section('title', 'FAQ')
@section('meta_description', 'Answers about Maison Modern guest checkout, Cash on Delivery, and order confirmation.')
@section('canonical', route('pages.faq'))

@section('content')
    <x-info-page
        kicker="Customer care"
        title="FAQ"
        intro="A short guide to shopping with Maison Modern."
        :crumbs="[
            ['label' => 'Home', 'href' => route('home')],
            ['label' => 'FAQ'],
        ]"
    >
        <x-info-section title="Do I need an account?">
            <p>No. You can browse, add to cart, and checkout without creating an account.</p>
        </x-info-section>

        <x-info-section title="How do I pay?">
            <p>Cash on Delivery. You pay when you receive your order. Read more on the <a href="{{ route('pages.cash-on-delivery') }}" class="underline underline-offset-4 text-charcoal">Cash on Delivery</a> page.</p>
        </x-info-section>

        <x-info-section title="How will I know my order is confirmed?">
            <p>You will see an order number on the confirmation page. We will then contact you by phone.</p>
        </x-info-section>

        <x-info-section title="Where do you deliver?">
            <p>Maison Modern delivers to all of Morocco. Expected delivery time is 3–7 days. See <a href="{{ route('pages.delivery') }}" class="underline underline-offset-4 text-charcoal">Delivery</a>.</p>
        </x-info-section>
    </x-info-page>
@endsection
