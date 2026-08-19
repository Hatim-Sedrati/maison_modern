<div
    x-show="menuOpen"
    x-cloak
    id="mobile-navigation"
    class="fixed inset-0 z-[60] lg:hidden"
    role="dialog"
    aria-modal="true"
    aria-label="Menu"
    @keydown.escape.window="menuOpen = false"
>
    <div
        class="absolute inset-0 bg-charcoal/40"
        @click="menuOpen = false"
        x-show="menuOpen"
        x-transition.opacity.duration.200ms
    ></div>

    <nav
        class="absolute left-0 top-0 flex h-full w-[min(100%,22rem)] flex-col overflow-y-auto bg-cream px-6 py-5"
        x-show="menuOpen"
        x-transition:enter="transition duration-300 ease-out"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition duration-200 ease-in"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
    >
        <div class="mb-10 flex items-center justify-between">
            <img src="{{ asset('images/logo.png') }}" alt="Maison Modern" class="h-7 w-auto max-w-[160px] object-contain">
            <button type="button" class="flex min-h-12 min-w-12 items-center justify-center text-[11px] uppercase tracking-[0.16em]" @click="menuOpen = false" aria-label="Close menu">
                Close
            </button>
        </div>

        <div class="flex flex-1 flex-col">
            <a href="{{ route('shop.new-arrivals') }}" class="min-h-12 py-3 text-lg tracking-wide" @click="menuOpen = false">New Arrivals</a>
            <a href="{{ route('shop.women') }}" class="min-h-12 py-3 text-lg tracking-wide" @click="menuOpen = false">Women</a>
            <a href="{{ route('shop.men') }}" class="min-h-12 py-3 text-lg tracking-wide" @click="menuOpen = false">Men</a>
            <a href="{{ route('shop.accessories') }}" class="min-h-12 py-3 text-lg tracking-wide" @click="menuOpen = false">Accessories</a>

            @if ($navCategories->isNotEmpty())
                <div class="my-5 border-t border-sand"></div>
                <p class="mb-1 text-[11px] uppercase tracking-[0.18em] text-muted">Categories</p>

                @foreach ($navCategories as $category)
                    <a
                        href="{{ route('category.show', $category) }}"
                        class="min-h-12 py-3 text-sm text-charcoal-light"
                        @click="menuOpen = false"
                    >
                        {{ $category->name }}
                    </a>
                @endforeach
            @endif

            <div class="mt-auto border-t border-sand pt-6">
                <a href="{{ route('pages.shipping') }}" class="block min-h-11 py-2 text-sm text-charcoal-light" @click="menuOpen = false">Delivery</a>
                <a href="{{ route('pages.contact') }}" class="block min-h-11 py-2 text-sm text-charcoal-light" @click="menuOpen = false">Contact</a>
            </div>
        </div>
    </nav>
</div>
