@extends('layouts.storefront')

@php
    $seoImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
    $seoDescription = \Illuminate\Support\Str::limit(trim(strip_tags($product->short_description ?: $product->description ?: $product->name.' — Maison Modern.')), 160);
@endphp

@section('title', $product->name)
@section('meta_description', $seoDescription)
@section('og_type', 'product')
@section('canonical', route('product.show', $product))
@section('og_image', $seoImage?->urlFor('card') ?? '')

@section('content')
    <div class="page-shell py-8 md:py-14">
        <x-breadcrumbs class="mb-8" :items="array_values(array_filter([
            ['label' => 'Home', 'href' => route('home')],
            $product->category ? ['label' => $product->category->name, 'href' => route('category.show', $product->category)] : ['label' => 'Shop', 'href' => route('shop.index')],
            ['label' => $product->name],
        ]))" />

        <div class="grid gap-10 lg:grid-cols-2 lg:items-start lg:gap-16">
            <livewire:product.gallery :product="$product" :key="'gallery-'.$product->id" />

            <div>
                @if ($product->gender)
                    <p class="text-[11px] uppercase tracking-[0.2em] text-muted">{{ $product->gender->getLabel() }}</p>
                @endif

                <h1 class="mt-2 font-display text-3xl leading-tight text-ink md:text-4xl">{{ $product->name }}</h1>

                @if ($product->short_description)
                    <p class="mt-4 text-sm leading-relaxed text-charcoal-light">{{ $product->short_description }}</p>
                @endif

                <livewire:product.add-to-cart :product="$product" :key="'cart-'.$product->id" />

                @if ($product->description)
                    <div class="mt-10 border-t border-sand pt-8">
                        <h2 class="text-[11px] uppercase tracking-[0.16em]">Details</h2>
                        <div class="mt-3 text-sm leading-relaxed text-charcoal-light">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>
                @endif

                <div class="mt-10 space-y-6 border-t border-sand pt-8">
                    <div>
                        <h2 class="text-[11px] uppercase tracking-[0.16em]">Delivery</h2>
                        <p class="mt-2 text-sm leading-relaxed text-charcoal-light">
                            We deliver across Morocco. Delivery fees are calculated at checkout and confirmed with your order.
                        </p>
                    </div>
                    <div>
                        <h2 class="text-[11px] uppercase tracking-[0.16em]">Cash on Delivery</h2>
                        <p class="mt-2 text-sm leading-relaxed text-charcoal-light">
                            Payment happens when your order arrives. No online payment is required.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
