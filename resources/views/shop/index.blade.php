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

    $pageIntro = match ($pageTitle) {
        'Women' => 'Contemporary pieces designed for everyday elegance.',
        'Men' => 'Refined essentials with a modern Moroccan sensibility.',
        'Accessories' => 'The finishing details that complete every look.',
        'New arrivals' => 'Discover the latest Maison Modern collection.',
        'Search' => null,
        'Unisex' => 'Pieces designed to move easily between wardrobes.',
        default => 'Modern Moroccan fashion for everyday elegance.',
    };

    $isSearch = filled($filters['search']);

    $canonicalParams = array_filter([
        'gender' => $filters['gender'] ?? null,
        'category' => $filters['category'] ?? null,
        'sort' => (($filters['sort'] ?? 'featured') !== 'featured') ? ($filters['sort'] ?? null) : null,
        'page' => $products->currentPage() > 1 ? $products->currentPage() : null,
    ], fn ($value) => $value !== null && $value !== '');

    $canonicalUrl = $isSearch
        ? route('shop.index')
        : route('shop.index', $canonicalParams);

    $breadcrumbs = [
        ['label' => 'Home', 'href' => route('home')],
    ];

    if ($pageTitle !== 'Shop') {
        $breadcrumbs[] = ['label' => 'Shop', 'href' => route('shop.index')];
    }

    $breadcrumbs[] = ['label' => $pageTitle];
@endphp

@section('title', $pageTitle)
@section('meta_description', $pageIntro ?: 'Search the Maison Modern collection.')
@section('canonical', $canonicalUrl)
@section('robots', $isSearch ? 'noindex, follow' : '')

@section('content')
    <div class="page-shell py-10 md:py-14">
        <x-breadcrumbs class="mb-8" :items="$breadcrumbs" />

        <header class="mb-10 md:mb-12">
            <p class="text-[11px] uppercase tracking-[0.22em] text-muted">Shop</p>
            <h1 class="mt-2 section-title">{{ $pageTitle }}</h1>
            @if ($isSearch)
                <p class="mt-3 text-sm text-charcoal-light">Results for “{{ $filters['search'] }}”</p>
            @elseif ($pageIntro)
                <p class="mt-4 max-w-xl text-sm leading-relaxed text-charcoal-light">{{ $pageIntro }}</p>
            @endif
        </header>

        <div class="grid gap-10 lg:grid-cols-[220px_minmax(0,1fr)] lg:gap-16">
            <aside>
                <x-product-filters :filters="$filters" :categories="$categories" />
            </aside>

            <div>
                <p class="mb-6 text-[11px] uppercase tracking-[0.16em] text-muted">{{ $products->total() }} {{ \Illuminate\Support\Str::plural('piece', $products->total()) }}</p>

                <x-product-grid
                    :products="$products"
                    :empty-title="$isSearch ? 'No products found' : 'This collection is currently empty.'"
                    :empty-text="$isSearch ? 'Try another search or explore our collections.' : 'New pieces will appear here as they are added.'"
                    :empty-href="route('shop.new-arrivals')"
                    empty-action="Continue Shopping"
                />

                @if ($products->hasPages())
                    <div class="mt-14">
                        {{ $products->links('pagination.storefront') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
