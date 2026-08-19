@extends('layouts.storefront')

@section('title', 'FAQ')

@section('content')
    <article class="mx-auto max-w-xl px-6 py-16 md:py-24">
        <p class="text-xs uppercase tracking-[0.3em] text-muted">Customer care</p>
        <h1 class="mt-3 section-title">FAQ</h1>
        <dl class="mt-10 space-y-8 text-sm leading-relaxed">
            <div>
                <dt class="text-xs uppercase tracking-widest">Do I need an account?</dt>
                <dd class="mt-2 text-charcoal-light">No. You can browse, add to cart, and checkout without creating an account.</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-widest">How do I pay?</dt>
                <dd class="mt-2 text-charcoal-light">Cash on delivery. You pay when you receive your order.</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-widest">How will I know my order is confirmed?</dt>
                <dd class="mt-2 text-charcoal-light">You will see an order number on the confirmation page. We will then contact you by phone.</dd>
            </div>
        </dl>
    </article>
@endsection
