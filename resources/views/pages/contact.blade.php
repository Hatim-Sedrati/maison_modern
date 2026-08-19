@extends('layouts.storefront')

@section('title', 'Contact')

@section('content')
    <article class="mx-auto max-w-xl px-5 py-16 md:py-24">
        <x-breadcrumbs class="mb-10" :items="[
            ['label' => 'Home', 'href' => route('home')],
            ['label' => 'Contact'],
        ]" />
        <p class="text-[11px] uppercase tracking-[0.28em] text-muted">Customer care</p>
        <h1 class="mt-3 section-title">Contact</h1>
        <div class="mt-8 space-y-4 text-sm leading-relaxed text-charcoal-light">
            <p>Orders are confirmed by phone after checkout. Please use a number we can reach you on when you place an order.</p>
            <p>We will contact you shortly after your order is received to confirm delivery details.</p>
        </div>
    </article>
@endsection
