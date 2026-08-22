@extends('layouts.storefront')

@section('title', 'Returns')
@section('meta_description', 'Request a Maison Modern return within 1 day of delivery. Used or customer-damaged items cannot be returned. Approved returns may be an exchange or a cash refund.')
@section('canonical', route('pages.returns'))

@section('content')
    <x-info-page
        title="Returns"
        intro="A return may be requested within 1 day after you receive your order."
        :crumbs="[
            ['label' => 'Home', 'href' => route('home')],
            ['label' => 'Returns'],
        ]"
    >
        <x-info-section title="When to request a return">
            <p>You may request a return within 1 day after receiving the order.</p>
        </x-info-section>

        <x-info-section title="Items we cannot return">
            <p>Items that have been used or damaged by the customer cannot be returned.</p>
        </x-info-section>

        <x-info-section title="If the return is approved">
            <p>For an approved return, Maison Modern offers either an exchange or a cash refund.</p>
        </x-info-section>

        <x-info-section title="How to request a return">
            <p>Request a return through the <a href="{{ route('pages.contact') }}" class="underline underline-offset-4 text-charcoal">Contact</a> page within 1 day after receiving the order.</p>
        </x-info-section>
    </x-info-page>
@endsection
