@extends('layouts.storefront')

@section('title', $category->name)

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 md:px-6 md:py-14">
        <nav class="mb-6 text-xs uppercase tracking-widest text-muted" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-charcoal">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('shop.index') }}" class="hover:text-charcoal">Shop</a>
            <span class="mx-2">/</span>
            <span class="text-charcoal">{{ $category->name }}</span>
        </nav>

        <header class="mb-10">
            <h1 class="section-title">{{ $category->name }}</h1>
            @if ($category->description)
                <p class="mt-4 max-w-2xl text-sm leading-relaxed text-charcoal-light">{{ $category->description }}</p>
            @endif
        </header>

        <div class="grid gap-10 lg:grid-cols-[240px_minmax(0,1fr)] lg:gap-14">
            <aside>
                <x-product-filters
                    :filters="$filters"
                    :categories="$categories"
                    :action="route('category.show', $category)"
                    :hide-category="true"
                />
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
