@extends('layouts.storefront')

@php
    $pageTitle = match (true) {
        filled($filters['search']) => 'Search',
        ($filters['gender'] ?? null) === 'women' => 'Women',
        ($filters['gender'] ?? null) === 'men' => 'Men',
        ($filters['gender'] ?? null) === 'unisex' => 'Unisex',
        ($filters['sort'] ?? null) === 'newest' && ! $filters['category'] => 'New arrivals',
        filled($filters['category']) => $categories->firstWhere('slug', $filters['category'])?->name ?? 'Shop',
        default => 'Shop',
    };
@endphp

@section('title', $pageTitle)

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 md:px-6 md:py-14">
        <header class="mb-10">
            <p class="text-xs uppercase tracking-[0.25em] text-muted">Shop</p>
            <h1 class="mt-2 section-title">{{ $pageTitle }}</h1>
            @if ($filters['search'])
                <p class="mt-3 text-sm text-muted">Results for “{{ $filters['search'] }}”</p>
            @endif
        </header>

        <div class="grid gap-10 lg:grid-cols-[240px_minmax(0,1fr)] lg:gap-14">
            <aside>
                <x-product-filters :filters="$filters" :categories="$categories" />
            </aside>

            <div>
                <p class="mb-6 text-xs uppercase tracking-widest text-muted">{{ $products->total() }} {{ \Illuminate\Support\Str::plural('piece', $products->total()) }}</p>

                <x-product-grid :products="$products" />

                @if ($products->hasPages())
                    <div class="mt-12">
                        {{ $products->links('pagination.storefront') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
