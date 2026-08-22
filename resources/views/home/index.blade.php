@extends('layouts.storefront')

@php
    $heroImage = $heroProduct?->images->firstWhere('is_primary', true) ?? $heroProduct?->images->first();
    $firstImage = function ($products) {
        $product = $products->first(fn ($item) => $item->images->isNotEmpty());

        return $product
            ? ($product->images->firstWhere('is_primary', true) ?? $product->images->first())
            : null;
    };
    $womenImage = $firstImage($women);
    $menImage = $firstImage($men);
    $accessoriesImage = $firstImage($accessories);
    $editorialImage = $heroImage
        ?? $firstImage($featured)
        ?? $firstImage($newArrivals);
@endphp

@section('title_full', 'Maison Modern — Moroccan Fashion')
@section('meta_description', 'Maison Modern — contemporary Moroccan fashion. Discover the latest collection for women, men, and accessories.')
@section('canonical', route('home'))
@if ($heroImage)
    @section('og_image', $heroImage->urlFor('card'))
@endif

@section('content')

    <section class="relative overflow-hidden bg-ivory">
        <div class="mx-auto grid max-w-7xl lg:grid-cols-2">
            <div class="flex flex-col justify-center px-5 py-14 md:px-12 md:py-20 lg:min-h-[560px] lg:py-24">
                <p class="text-[11px] uppercase tracking-[0.28em] text-muted">New collection</p>
                <h1 class="mt-4 max-w-lg font-display text-[2.35rem] leading-[1.12] text-ink md:text-5xl lg:text-[3.4rem]">
                    Discover the latest Maison Modern collection.
                </h1>
                <p class="mt-6 max-w-md text-sm leading-relaxed text-charcoal-light md:text-base">
                    Contemporary Moroccan fashion, designed to feel considered, warm, and easy to wear.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('shop.new-arrivals') }}" class="btn-primary">Shop New Arrivals</a>
                    <a href="{{ route('shop.index') }}" class="btn-secondary">Explore Collection</a>
                </div>
            </div>

            <div class="min-h-[280px] bg-ivory-dark sm:min-h-[360px] lg:min-h-[560px]">
                @if ($heroImage)
                    <img
                        src="{{ $heroImage->urlFor('hero') }}"
                        alt="{{ $heroProduct->name }}"
                        class="h-full w-full object-cover"
                        fetchpriority="high"
                        decoding="async"
                    >
                @else
                    <div class="flex h-full min-h-[280px] items-center justify-center sm:min-h-[360px] lg:min-h-[560px]">
                        <p class="px-8 text-center font-display text-3xl tracking-[0.18em] uppercase text-taupe">Maison Modern</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    @if ($newArrivals->isNotEmpty())
        <section class="page-shell py-16 md:py-24">
            <x-section-heading
                kicker="Just in"
                title="New arrivals"
                intro="The newest pieces from the Maison Modern collection."
                :href="route('shop.new-arrivals')"
                action="View all"
            />
            <x-product-grid :products="$newArrivals" />
            <div class="mt-8 text-center sm:hidden">
                <a href="{{ route('shop.new-arrivals') }}" class="nav-link">View all</a>
            </div>
        </section>
    @endif

    <section class="bg-ivory">
        <div class="page-shell py-16 md:py-24">
            <x-section-heading
                kicker="Collections"
                title="Shop by category"
                intro="Find what you need, without the noise."
            />
            <div class="grid gap-3 md:grid-cols-3 md:gap-4">
                <x-destination-tile title="Women" :href="route('shop.women')" :image="$womenImage" alt="Women collection" />
                <x-destination-tile title="Men" :href="route('shop.men')" :image="$menImage" alt="Men collection" />
                <x-destination-tile
                    title="Accessories"
                    :href="$accessoriesCategory ? route('category.show', $accessoriesCategory) : route('shop.accessories')"
                    :image="$accessoriesImage"
                    alt="Accessories collection"
                />
            </div>
        </div>
    </section>

    <section class="grid lg:grid-cols-2">
        <div class="flex flex-col justify-center bg-charcoal px-6 py-16 text-ivory md:px-12 md:py-24">
            <p class="text-[11px] uppercase tracking-[0.28em] text-sand">The collection</p>
            <h2 class="mt-4 max-w-md font-display text-3xl leading-tight md:text-4xl">
                Designed for everyday elegance.
            </h2>
            <p class="mt-6 max-w-md text-sm leading-relaxed text-sand">
                Quiet luxury with a Moroccan sensibility. Pieces meant to be worn, not just seen — refined, warm, and contemporary.
            </p>
            <div class="mt-8">
                <a href="{{ route('shop.index') }}" class="inline-flex min-h-12 items-center border border-ivory/70 px-7 text-[11px] font-medium uppercase tracking-[0.18em] text-ivory transition-colors hover:bg-ivory hover:text-charcoal">
                    Explore Collection
                </a>
            </div>
        </div>
        <div class="min-h-[280px] bg-ivory-dark lg:min-h-full">
            @if ($editorialImage)
                <img
                    src="{{ $editorialImage->urlFor('hero') }}"
                    alt=""
                    class="h-full w-full object-cover"
                    loading="lazy"
                    decoding="async"
                >
            @else
                <div class="flex h-full min-h-[280px] items-center justify-center lg:min-h-[420px]">
                    <p class="px-8 text-center font-display text-2xl tracking-[0.2em] uppercase text-taupe">Maison Modern</p>
                </div>
            @endif
        </div>
    </section>

    @if ($featured->isNotEmpty())
        <section class="page-shell py-16 md:py-24">
            <x-section-heading
                kicker="Selected"
                title="Featured collection"
                intro="A considered edit of pieces we love right now."
                :href="route('shop.index')"
                action="Shop all"
            />
            <x-product-grid :products="$featured" />
        </section>
    @endif

    <section class="border-t border-sand/80 bg-cream">
        <div class="mx-auto max-w-2xl px-6 py-20 text-center md:py-28">
            <p class="text-[11px] uppercase tracking-[0.28em] text-muted">Maison Modern</p>
            <p class="mt-6 font-display text-2xl leading-relaxed text-ink md:text-3xl">
                Clothing designed in Morocco for everyday elegance — warm, modern, and quietly confident.
            </p>
        </div>
    </section>

    <section class="bg-ivory">
        <div class="page-shell grid gap-10 py-14 md:grid-cols-3 md:gap-8 md:py-16">
            <div>
                <h2 class="text-[11px] uppercase tracking-[0.18em]"><a href="{{ route('pages.delivery') }}" class="hover:text-muted">Delivery</a></h2>
                <p class="mt-3 text-sm leading-relaxed text-charcoal-light">We deliver across Morocco. Fees are shown before you confirm your order.</p>
            </div>
            <div>
                <h2 class="text-[11px] uppercase tracking-[0.18em]"><a href="{{ route('pages.cash-on-delivery') }}" class="hover:text-muted">Cash on Delivery</a></h2>
                <p class="mt-3 text-sm leading-relaxed text-charcoal-light">Pay when your order arrives. No online payment is required.</p>
            </div>
            <div>
                <h2 class="text-[11px] uppercase tracking-[0.18em]"><a href="{{ route('pages.contact') }}" class="hover:text-muted">Questions?</a></h2>
                <p class="mt-3 text-sm leading-relaxed text-charcoal-light">We confirm every order by phone. <a href="{{ route('pages.contact') }}" class="underline underline-offset-4">Contact us</a>.</p>
            </div>
        </div>
    </section>
@endsection
