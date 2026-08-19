@extends('layouts.storefront')

@section('title', $category->name)

@section('content')
    <div class="page-shell py-10 md:py-14">
        <x-breadcrumbs class="mb-8" :items="[
            ['label' => 'Home', 'href' => route('home')],
            ['label' => 'Shop', 'href' => route('shop.index')],
            ['label' => $category->name],
        ]" />

        <header class="mb-10 md:mb-12">
            <h1 class="section-title">{{ $category->name }}</h1>
            @if ($category->description)
                <p class="mt-4 max-w-2xl text-sm leading-relaxed text-charcoal-light">{{ $category->description }}</p>
            @else
                <p class="mt-4 max-w-2xl text-sm leading-relaxed text-charcoal-light">Contemporary pieces designed for everyday elegance.</p>
            @endif
        </header>

        <div class="grid gap-10 lg:grid-cols-[220px_minmax(0,1fr)] lg:gap-16">
            <aside>
                <x-product-filters
                    :filters="$filters"
                    :categories="$categories"
                    :action="route('category.show', $category)"
                    :hide-category="true"
                />
            </aside>

            <div>
                <p class="mb-6 text-[11px] uppercase tracking-[0.16em] text-muted">{{ $products->total() }} {{ \Illuminate\Support\Str::plural('piece', $products->total()) }}</p>

                <x-product-grid
                    :products="$products"
                    empty-title="This collection is currently empty."
                    empty-text="New pieces will appear here as they are added."
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
