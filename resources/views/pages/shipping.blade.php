@extends('layouts.storefront')

@section('title', 'Shipping')

@section('content')
    <article class="mx-auto max-w-xl px-5 py-16 md:py-24">
        <x-breadcrumbs class="mb-10" :items="[
            ['label' => 'Home', 'href' => route('home')],
            ['label' => 'Delivery'],
        ]" />
        <p class="text-[11px] uppercase tracking-[0.28em] text-muted">Information</p>
        <h1 class="mt-3 section-title">Delivery</h1>
        <div class="mt-8 space-y-4 text-sm leading-relaxed text-charcoal-light">
            <p>Maison Modern delivers within Morocco. Delivery fees are calculated when you place your order and shown before you confirm.</p>
            <p>Payment is Cash on Delivery. You pay when you receive your order.</p>
        </div>
    </article>
@endsection
