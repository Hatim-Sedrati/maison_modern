@props(['product'])

@php
    $images = $product->images;
    $primary = $images->firstWhere('is_primary', true) ?? $images->first();
    $secondary = $primary
        ? $images->first(fn ($image) => $image->id !== $primary->id)
        : null;
    $colors = $product->relationLoaded('variants')
        ? $product->variants->pluck('color')->filter()->unique()->values()
        : collect();
@endphp

<article {{ $attributes->merge(['class' => 'group']) }}>
    <a href="{{ route('product.show', $product) }}" class="block">
        <div class="relative overflow-hidden bg-ivory-dark">
            @if ($primary)
                <x-product-image
                    :image="$primary"
                    :alt="$product->name"
                    preset="card"
                    class="product-card-image"
                    width="800"
                    height="1067"
                />
                @if ($secondary)
                    <x-product-image
                        :image="$secondary"
                        alt=""
                        preset="card"
                        class="product-card-image absolute inset-0 hidden opacity-0 transition-opacity duration-300 group-hover:opacity-100 md:block"
                        width="800"
                        height="1067"
                        aria-hidden="true"
                    />
                @endif
            @else
                <div class="product-card-image flex items-center justify-center">
                    <span class="px-4 text-center text-[10px] uppercase tracking-[0.25em] text-muted">Maison Modern</span>
                </div>
            @endif
        </div>

        <div class="pt-3">
            @if ($product->category)
                <p class="mb-1 text-[10px] uppercase tracking-[0.18em] text-muted">{{ $product->category->name }}</p>
            @endif

            <h3 class="font-sans text-sm leading-snug text-charcoal">{{ $product->name }}</h3>

            <p class="mt-1 text-sm text-charcoal-light">{{ \App\Support\Money::format($product->price) }}</p>

            @if ($colors->isNotEmpty())
                <div class="mt-2 flex flex-wrap gap-1.5" aria-label="Available colors">
                    @foreach ($colors as $color)
                        <span
                            class="inline-block h-2.5 w-2.5 rounded-full border border-charcoal/20"
                            style="background-color: {{ \App\Support\ColorSwatch::hex($color) }}"
                            title="{{ $color }}"
                        >
                            <span class="sr-only">{{ $color }}</span>
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </a>
</article>
