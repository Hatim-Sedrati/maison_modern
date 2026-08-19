@php
    $images = $product->images;
    $count = $images->count();
    $active = $images->get($activeIndex) ?? $images->first();
@endphp

<div>
    @if ($count === 0)
        <div class="aspect-[3/4] flex items-center justify-center bg-ivory-dark">
            <p class="px-6 text-center text-xs uppercase tracking-[0.25em] text-muted">Image coming soon</p>
        </div>
    @else
        {{-- Mobile: native swipe --}}
        <div class="lg:hidden" wire:ignore>
            <div
                class="flex snap-x snap-mandatory overflow-x-auto scrollbar-none"
                aria-label="Product images"
            >
                @foreach ($images as $index => $image)
                    <div class="w-full shrink-0 snap-center">
                        <img
                            src="{{ $image->urlFor('gallery') }}"
                            alt="{{ $product->name }}{{ $count > 1 ? ' — image '.($index + 1) : '' }}"
                            class="aspect-[3/4] w-full object-cover"
                            @if ($index === 0) fetchpriority="high" loading="eager" @else loading="lazy" @endif
                            decoding="async"
                        >
                    </div>
                @endforeach
            </div>
            @if ($count > 1)
                <p class="mt-3 text-center text-[10px] uppercase tracking-widest text-muted">Swipe to view more</p>
            @endif
        </div>

        {{-- Desktop: main image + thumbnails --}}
        <div class="hidden lg:block">
            <div class="bg-ivory-dark">
                @if ($active)
                    <img
                        src="{{ $active->urlFor('gallery') }}"
                        alt="{{ $product->name }}"
                        class="aspect-[3/4] w-full object-cover"
                        fetchpriority="high"
                        decoding="async"
                    >
                @endif
            </div>

            @if ($count > 1)
                <div class="mt-3 flex gap-2" role="list">
                    @foreach ($images as $index => $image)
                        <button
                            type="button"
                            wire:click="selectImage({{ $index }})"
                            class="w-16 shrink-0 overflow-hidden border {{ $activeIndex === $index ? 'border-charcoal' : 'border-transparent' }}"
                            aria-label="View image {{ $index + 1 }}"
                            aria-current="{{ $activeIndex === $index ? 'true' : 'false' }}"
                        >
                            <img
                                src="{{ $image->urlFor('thumb') }}"
                                alt=""
                                class="aspect-[3/4] w-full object-cover"
                                loading="lazy"
                                decoding="async"
                            >
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
