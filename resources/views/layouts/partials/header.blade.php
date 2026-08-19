@php
    $isWomen = request('gender') === 'women';
    $isMen = request('gender') === 'men';
    $isNewest = request('sort') === 'newest' && ! request('gender') && ! request('category') && ! request('search');
    $isAccessories = request('category') === 'accessories'
        || (request()->routeIs('category.show') && request()->route('category')?->slug === 'accessories');
@endphp

<header
    class="sticky top-0 z-50 border-b border-sand/70 bg-cream/90 backdrop-blur-md"
    x-data="{ menuOpen: false, searchOpen: {{ request('search') ? 'true' : 'false' }} }"
    x-effect="document.body.classList.toggle('overflow-hidden', menuOpen)"
>
    {{-- Mobile header --}}
    <div class="relative flex items-center justify-between px-4 py-3 lg:hidden">
        <button
            type="button"
            class="flex min-h-11 min-w-11 items-center justify-center"
            @click="menuOpen = true"
            :aria-expanded="menuOpen.toString()"
            aria-controls="mobile-navigation"
            aria-label="Open menu"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
            </svg>
        </button>

        <a href="{{ route('home') }}" class="absolute left-1/2 -translate-x-1/2">
            <img
                src="{{ asset('images/logo.png') }}"
                alt="Maison Modern"
                class="h-7 w-auto max-w-[160px] object-contain sm:h-8"
            >
        </a>

        <div class="flex items-center">
            <button
                type="button"
                class="flex min-h-11 min-w-11 items-center justify-center"
                @click="searchOpen = !searchOpen"
                :aria-expanded="searchOpen.toString()"
                aria-controls="header-search-panel"
                aria-label="Search"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
                </svg>
            </button>

            <a href="{{ route('cart.index') }}" class="relative flex min-h-11 min-w-11 items-center justify-center" aria-label="Cart">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
                @livewire('layout.cart-count', key('cart-count-mobile'))
            </a>
        </div>
    </div>

    {{-- Desktop header --}}
    <div class="page-shell hidden grid-cols-3 items-center py-5 lg:grid">
        <nav class="flex items-center gap-8" aria-label="Primary">
            <a href="{{ route('shop.new-arrivals') }}" class="nav-link {{ $isNewest ? 'nav-link-active' : '' }}">New Arrivals</a>
            <a href="{{ route('shop.women') }}" class="nav-link {{ $isWomen ? 'nav-link-active' : '' }}">Women</a>
            <a href="{{ route('shop.men') }}" class="nav-link {{ $isMen ? 'nav-link-active' : '' }}">Men</a>
            <a href="{{ route('shop.accessories') }}" class="nav-link {{ $isAccessories ? 'nav-link-active' : '' }}">Accessories</a>
        </nav>

        <a href="{{ route('home') }}" class="justify-self-center">
            <img
                src="{{ asset('images/logo.png') }}"
                alt="Maison Modern"
                class="h-10 w-auto max-w-[240px] object-contain"
            >
        </a>

        <div class="flex items-center justify-end gap-7">
            <button
                type="button"
                class="nav-link"
                @click="searchOpen = !searchOpen"
                :aria-expanded="searchOpen.toString()"
                aria-controls="header-search-panel"
            >
                Search
            </button>

            <a href="{{ route('cart.index') }}" class="relative nav-link flex items-center gap-1">
                Cart
                @livewire('layout.cart-count', key('cart-count-desktop'))
            </a>
        </div>
    </div>

    {{-- Search bar --}}
    <div
        id="header-search-panel"
        x-show="searchOpen"
        x-cloak
        x-transition.opacity.duration.200ms
        class="border-t border-sand/70 bg-ivory px-4 py-4 lg:px-6"
    >
        <form action="{{ route('shop.index') }}" method="GET" class="mx-auto flex max-w-xl gap-2">
            <label for="header-search" class="sr-only">Search products</label>
            <input
                id="header-search"
                type="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search by name or description"
                class="input-field flex-1"
                x-ref="searchInput"
                x-effect="if (searchOpen) $nextTick(() => $refs.searchInput.focus())"
            >
            <button type="submit" class="btn-primary shrink-0">Search</button>
        </form>
    </div>

    @include('layouts.partials.mobile-menu')
</header>
