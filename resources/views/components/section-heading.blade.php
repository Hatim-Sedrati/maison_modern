@props([
    'kicker' => null,
    'title',
    'href' => null,
    'action' => null,
    'intro' => null,
])

<div {{ $attributes->merge(['class' => 'mb-10 flex flex-col gap-4 sm:mb-12 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div>
        @if ($kicker)
            <p class="text-[11px] uppercase tracking-[0.22em] text-muted">{{ $kicker }}</p>
        @endif
        <h2 class="section-title {{ $kicker ? 'mt-2' : '' }}">{{ $title }}</h2>
        @if ($intro)
            <p class="mt-3 max-w-xl text-sm leading-relaxed text-charcoal-light">{{ $intro }}</p>
        @endif
    </div>
    @if ($href && $action)
        <a href="{{ $href }}" class="nav-link hidden sm:inline-flex">{{ $action }}</a>
    @endif
</div>
