@extends('layouts.storefront')

@section('title', $product->name)

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 md:px-6 md:py-14">
        <nav class="mb-8 text-xs uppercase tracking-widest text-muted" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-charcoal">Home</a>
            <span class="mx-2">/</span>
            @if ($product->category)
                <a href="{{ route('category.show', $product->category) }}" class="hover:text-charcoal">{{ $product->category->name }}</a>
                <span class="mx-2">/</span>
            @endif
            <span class="text-charcoal">{{ $product->name }}</span>
        </nav>

        <div class="grid gap-10 lg:grid-cols-2 lg:gap-16">
            <livewire:product.gallery :product="$product" :key="'gallery-'.$product->id" />

            <div>
                @if ($product->gender)
                    <p class="text-xs uppercase tracking-[0.2em] text-muted">{{ $product->gender->getLabel() }}</p>
                @endif

                <h1 class="mt-2 font-display text-3xl md:text-4xl">{{ $product->name }}</h1>

                @if ($product->short_description)
                    <p class="mt-4 text-sm leading-relaxed text-charcoal-light">{{ $product->short_description }}</p>
                @endif

                <livewire:product.add-to-cart :product="$product" :key="'cart-'.$product->id" />

                @if ($product->description)
                    <div class="mt-10 border-t border-ivory-dark pt-8">
                        <h2 class="text-xs uppercase tracking-widest">Details</h2>
                        <div class="mt-3 text-sm leading-relaxed text-charcoal-light">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
