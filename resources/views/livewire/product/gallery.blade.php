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
        {{-- Mobile: swipeable gallery --}}
        <div
            class="relative lg:hidden"
            wire:ignore
            x-data="{
                index: 0,
                count: {{ $count }},
                start: 0,
                next() { this.index = (this.index + 1) % this.count },
                prev() { this.index = (this.index - 1 + this.count) % this.count },
            }"
        >
            <div
                class="overflow-hidden bg-ivory-dark"
                @touchstart.passive="start = $event.changedTouches[0].clientX"
                @touchend.passive="
                    const dx = $event.changedTouches[0].clientX - start;
                    if (Math.abs(dx) > 40) dx < 0 ? next() : prev();
                "
            >
                <div
                    class="flex transition-transform duration-300 ease-out"
                    :style="'transform: translateX(-' + (index * 100) + '%)'"
                >
                    @foreach ($images as $index => $image)
                        <div class="w-full shrink-0">
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
            </div>

            @if ($count > 1)
                <button
                    type="button"
                    class="absolute left-2 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center bg-cream/80 text-charcoal"
                    @click="prev()"
                    aria-label="Previous image"
                >
                    <span aria-hidden="true">‹</span>
                </button>
                <button
                    type="button"
                    class="absolute right-2 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center bg-cream/80 text-charcoal"
                    @click="next()"
                    aria-label="Next image"
                >
                    <span aria-hidden="true">›</span>
                </button>

                <div class="mt-4 flex justify-center gap-2" role="tablist" aria-label="Product images">
                    @foreach ($images as $index => $image)
                        <button
                            type="button"
                            class="h-1.5 w-1.5 rounded-full"
                            :class="index === {{ $index }} ? 'bg-charcoal' : 'bg-sand'"
                            @click="index = {{ $index }}"
                            aria-label="View image {{ $index + 1 }}"
                        ></button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Desktop: main image, controls, thumbnails --}}
        <div class="hidden lg:block">
            <div class="relative bg-ivory-dark">
                @if ($active)
                    <img
                        src="{{ $active->urlFor('gallery') }}"
                        alt="{{ $product->name }}"
                        class="aspect-[3/4] w-full object-cover"
                        wire:key="gallery-main-{{ $activeIndex }}"
                        fetchpriority="high"
                        decoding="async"
                    >
                @endif

                @if ($count > 1)
                    <button
                        type="button"
                        wire:click="previous"
                        class="absolute left-3 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center bg-cream/85 text-lg text-charcoal transition-colors hover:bg-cream"
                        aria-label="Previous image"
                    >
                        <span aria-hidden="true">‹</span>
                    </button>
                    <button
                        type="button"
                        wire:click="next"
                        class="absolute right-3 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center bg-cream/85 text-lg text-charcoal transition-colors hover:bg-cream"
                        aria-label="Next image"
                    >
                        <span aria-hidden="true">›</span>
                    </button>
                    <p class="absolute bottom-3 right-3 bg-cream/85 px-2 py-1 text-[10px] uppercase tracking-widest text-muted">
                        {{ $activeIndex + 1 }} / {{ $count }}
                    </p>
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
