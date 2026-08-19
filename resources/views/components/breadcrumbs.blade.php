@props(['items'])

<nav {{ $attributes->merge(['class' => 'text-[11px] uppercase tracking-[0.16em] text-muted', 'aria-label' => 'Breadcrumb']) }}>
    @foreach ($items as $item)
        @if (! empty($item['href']) && ! $loop->last)
            <a href="{{ $item['href'] }}" class="transition-colors hover:text-charcoal">{{ $item['label'] }}</a>
        @else
            <span class="{{ $loop->last ? 'text-charcoal' : '' }}">{{ $item['label'] }}</span>
        @endif
        @if (! $loop->last)
            <span class="mx-2 text-sand">/</span>
        @endif
    @endforeach
</nav>
