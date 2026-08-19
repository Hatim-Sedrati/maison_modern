@extends('layouts.storefront')

@section('title', 'Returns')

@section('content')
    <article class="mx-auto max-w-xl px-5 py-16 md:py-24">
        <x-breadcrumbs class="mb-10" :items="[
            ['label' => 'Home', 'href' => route('home')],
            ['label' => 'Returns'],
        ]" />
        <p class="text-[11px] uppercase tracking-[0.28em] text-muted">Information</p>
        <h1 class="mt-3 section-title">Returns</h1>
        <div class="mt-8 space-y-4 text-sm leading-relaxed text-charcoal-light">
            <p>If an item is not right, contact us after delivery and we will help you with the next step.</p>
            <p>Please keep the piece unworn, with its original packaging, until we have confirmed the return.</p>
        </div>
    </article>
@endsection
