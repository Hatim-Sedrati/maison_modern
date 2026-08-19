@props([
    'filters',
    'categories',
    'action' => null,
    'hideCategory' => false,
])

@php
    $action = $action ?? route('shop.index');
@endphp

<div
    x-data="{ open: false }"
    @keydown.escape.window="open = false"
    x-effect="document.body.classList.toggle('overflow-hidden', open)"
>
    <div class="flex items-center justify-between gap-3 lg:hidden">
        <button
            type="button"
            class="btn-secondary w-full"
            @click="open = true"
            :aria-expanded="open.toString()"
            aria-controls="shop-filters-drawer"
        >
            Filter &amp; sort
        </button>
    </div>

    <form method="GET" action="{{ $action }}" class="mt-0 hidden space-y-8 lg:block">
        @if ($filters['search'])
            <input type="hidden" name="search" value="{{ $filters['search'] }}">
        @endif

        @include('components.partials.filter-fields', ['sortId' => 'desktop', 'hideCategory' => $hideCategory, 'filters' => $filters, 'categories' => $categories])

        <button type="submit" class="btn-primary w-full">Apply</button>
    </form>

    <div
        id="shop-filters-drawer"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-[60] lg:hidden"
        role="dialog"
        aria-modal="true"
        aria-label="Filter and sort"
    >
        <div class="absolute inset-0 bg-charcoal/40" @click="open = false" x-transition.opacity.duration.200ms></div>

        <form
            method="GET"
            action="{{ $action }}"
            class="absolute inset-x-0 bottom-0 max-h-[85vh] overflow-y-auto bg-cream px-5 pb-8 pt-5"
            x-show="open"
            x-transition:enter="transition duration-300 ease-out"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition duration-200 ease-in"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            @click.stop
        >
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-[11px] uppercase tracking-[0.18em]">Filter &amp; sort</h2>
                <button type="button" class="min-h-11 px-2 text-[11px] uppercase tracking-[0.16em]" @click="open = false" aria-label="Close filters">
                    Close
                </button>
            </div>

            @if ($filters['search'])
                <input type="hidden" name="search" value="{{ $filters['search'] }}">
            @endif

            <div class="space-y-7">
                @include('components.partials.filter-fields', ['sortId' => 'mobile', 'hideCategory' => $hideCategory, 'filters' => $filters, 'categories' => $categories])
            </div>

            <button type="submit" class="btn-primary mt-8 w-full">Apply</button>
        </form>
    </div>
</div>
