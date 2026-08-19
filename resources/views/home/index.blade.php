@extends('layouts.storefront')

@section('title', 'Home')

@section('content')
    @php
        $heroImage = $heroProduct?->images->firstWhere('is_primary', true) ?? $heroProduct?->images->first();
    @endphp

    <section class="relative overflow-hidden bg-ivory-dark/60">
        <div class="mx-auto grid max-w-7xl lg:grid-cols-2">
            <div class="flex flex-col justify-center px-6 py-16 md:px-12 md:py-24 lg:py-32">
                <p class="text-xs uppercase tracking-[0.3em] text-muted">New collection</p>
                <h1 class="mt-4 max-w-md text-4xl leading-tight md:text-5xl lg:text-6xl">
                    Discover the latest Maison Modern collection.
                </h1>
                <p class="mt-6 max-w-sm text-sm leading-relaxed text-charcoal-light">
                    Contemporary Moroccan fashion, designed to feel considered, warm, and easy to wear.
                </p>
                <div class="mt-8">
                    <a href="{{ route('shop.index') }}" class="btn-primary">Shop now</a>
                </div>
            </div>

            <div class="min-h-[320px] bg-ivory-dark lg:min-h-[560px]">
                @if ($heroImage)
                    <img
                        src="{{ $heroImage->url }}"
                        alt="{{ $heroProduct->name }}"
                        class="h-full w-full object-cover"
                        fetchpriority="high"
                        decoding="async"
                    >
                @else
                    <div class="flex h-full min-h-[320px] items-center justify-center lg:min-h-[560px]">
                        <p class="px-8 text-center font-display text-3xl tracking-[0.2em] uppercase text-warm">Maison Modern</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    @if ($newArrivals->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16 md:px-6 md:py-20">
            <div class="mb-10 flex items-end justify-between gap-4">
                <h2 class="section-title">New arrivals</h2>
                <a href="{{ route('shop.new-arrivals') }}" class="nav-link hidden sm:inline-flex">View all</a>
            </div>
            <x-product-grid :products="$newArrivals" />
            <div class="mt-8 text-center sm:hidden">
                <a href="{{ route('shop.new-arrivals') }}" class="nav-link">View all</a>
            </div>
        </section>
    @endif

    @if ($featured->isNotEmpty())
        <section class="bg-ivory-dark/40">
            <div class="mx-auto max-w-7xl px-4 py-16 md:px-6 md:py-20">
                <div class="mb-10 flex items-end justify-between gap-4">
                    <h2 class="section-title">Featured</h2>
                    <a href="{{ route('shop.index') }}" class="nav-link hidden sm:inline-flex">Shop all</a>
                </div>
                <x-product-grid :products="$featured" />
            </div>
        </section>
    @endif

    @if ($women->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16 md:px-6 md:py-20">
            <div class="mb-10 flex items-end justify-between gap-4">
                <h2 class="section-title">Women</h2>
                <a href="{{ route('shop.women') }}" class="nav-link hidden sm:inline-flex">Shop women</a>
            </div>
            <x-product-grid :products="$women" />
            <div class="mt-8 text-center sm:hidden">
                <a href="{{ route('shop.women') }}" class="nav-link">Shop women</a>
            </div>
        </section>
    @endif

    @if ($men->isNotEmpty())
        <section class="bg-ivory-dark/40">
            <div class="mx-auto max-w-7xl px-4 py-16 md:px-6 md:py-20">
                <div class="mb-10 flex items-end justify-between gap-4">
                    <h2 class="section-title">Men</h2>
                    <a href="{{ route('shop.men') }}" class="nav-link hidden sm:inline-flex">Shop men</a>
                </div>
                <x-product-grid :products="$men" />
                <div class="mt-8 text-center sm:hidden">
                    <a href="{{ route('shop.men') }}" class="nav-link">Shop men</a>
                </div>
            </div>
        </section>
    @endif

    @if ($accessories->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16 md:px-6 md:py-20">
            <div class="mb-10 flex items-end justify-between gap-4">
                <h2 class="section-title">Accessories</h2>
                <a href="{{ $accessoriesCategory ? route('category.show', $accessoriesCategory) : route('shop.accessories') }}" class="nav-link hidden sm:inline-flex">Shop accessories</a>
            </div>
            <x-product-grid :products="$accessories" />
        </section>
    @endif

    @if ($newArrivals->isEmpty() && $featured->isEmpty() && $women->isEmpty() && $men->isEmpty() && $accessories->isEmpty())
        <section class="mx-auto max-w-7xl px-4">
            <x-empty-state
                title="The collection is being prepared"
                text="New pieces will appear here as they are added."
                :href="route('shop.index')"
                action="Visit the shop"
            />
        </section>
    @endif

    <section class="border-t border-ivory-dark">
        <div class="mx-auto max-w-2xl px-6 py-20 text-center md:py-28">
            <p class="text-xs uppercase tracking-[0.3em] text-muted">Maison Modern</p>
            <p class="mt-6 font-display text-2xl leading-relaxed md:text-3xl">
                Clothing designed in Morocco for everyday elegance — warm, modern, and quietly confident.
            </p>
        </div>
    </section>
@endsection
