@extends('layouts.storefront')

@section('title', 'Delivery')
@section('meta_description', 'Maison Modern delivers to all of Morocco. Expected delivery time is 3–7 days. Payment is Cash on Delivery.')
@section('canonical', route('pages.delivery'))

@section('content')
    <x-info-page
        title="Delivery"
        intro="Maison Modern delivers to all of Morocco. Expected delivery time is 3–7 days."
        :crumbs="[
            ['label' => 'Home', 'href' => route('home')],
            ['label' => 'Delivery'],
        ]"
    >
        <x-info-section title="Where we deliver">
            <p>We deliver throughout Morocco.</p>
        </x-info-section>

        <x-info-section title="Delivery time">
            <p>Expected delivery time is 3–7 days.</p>
        </x-info-section>

        <x-info-section title="Payment">
            <p>Payment is <a href="{{ route('pages.cash-on-delivery') }}" class="underline underline-offset-4 text-charcoal">Cash on Delivery</a>. You pay when you receive your order.</p>
        </x-info-section>

        <x-info-section title="Questions">
            <p>If you have a question about an order or a delivery, see <a href="{{ route('pages.contact') }}" class="underline underline-offset-4 text-charcoal">Contact</a>.</p>
        </x-info-section>
    </x-info-page>
@endsection
