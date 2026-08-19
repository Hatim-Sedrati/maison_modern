<div
    x-show="menuOpen"
    x-cloak
    class="fixed inset-0 z-[60] lg:hidden"
    role="dialog"
    aria-modal="true"
    aria-label="Menu"
>
    <div class="absolute inset-0 bg-charcoal/40" @click="menuOpen = false"></div>

    <nav class="absolute left-0 top-0 flex h-full w-[min(100%,320px)] flex-col overflow-y-auto bg-ivory p-6">
        <div class="mb-8 flex items-center justify-between">
            <img src="{{ asset('images/logo.png') }}" alt="Maison Modern" class="h-8 w-auto max-w-[180px] object-contain">
            <button type="button" class="min-h-12 px-3 text-sm uppercase tracking-widest" @click="menuOpen = false" aria-label="Close menu">
                Close
            </button>
        </div>

        <div class="flex flex-col">
            <a href="{{ route('shop.new-arrivals') }}" class="min-h-12 py-3 text-sm uppercase tracking-widest" @click="menuOpen = false">New Arrivals</a>
            <a href="{{ route('shop.women') }}" class="min-h-12 py-3 text-sm uppercase tracking-widest" @click="menuOpen = false">Women</a>
            <a href="{{ route('shop.men') }}" class="min-h-12 py-3 text-sm uppercase tracking-widest" @click="menuOpen = false">Men</a>
            <a href="{{ route('shop.accessories') }}" class="min-h-12 py-3 text-sm uppercase tracking-widest" @click="menuOpen = false">Accessories</a>

            <div class="my-4 border-t border-ivory-dark"></div>
            <p class="mb-2 text-xs uppercase tracking-widest text-muted">Categories</p>

            @forelse ($navCategories as $category)
                <a
                    href="{{ route('category.show', $category) }}"
                    class="min-h-12 py-3 text-sm text-charcoal-light"
                    @click="menuOpen = false"
                >
                    {{ $category->name }}
                </a>
            @empty
                <p class="py-2 text-sm text-muted">Categories will appear here.</p>
            @endforelse

            <div class="my-4 border-t border-ivory-dark"></div>

            <form action="{{ route('shop.index') }}" method="GET" class="mt-2">
                <label for="mobile-search" class="mb-2 block text-xs uppercase tracking-widest text-muted">Search</label>
                <div class="flex gap-2">
                    <input
                        id="mobile-search"
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search products"
                        class="input-field"
                    >
                    <button type="submit" class="btn-primary shrink-0 px-4">Go</button>
                </div>
            </form>
        </div>
    </nav>
</div>
