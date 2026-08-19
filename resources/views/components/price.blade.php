@props([
    'amount',
    'compare' => null,
])

<span {{ $attributes }}>
    @if ($compare && \App\Support\Money::toCents(\App\Support\Money::of($compare)) > \App\Support\Money::toCents(\App\Support\Money::of($amount)))
        <span class="mr-2 text-muted line-through">{{ \App\Support\Money::format($compare) }}</span>
    @endif
    {{ \App\Support\Money::format($amount) }}
</span>
