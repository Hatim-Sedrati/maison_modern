@props([
    'title',
    'href',
    'image' => null,
    'alt' => '',
])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'group relative block min-h-[280px] overflow-hidden bg-ivory-dark md:min-h-[420px]']) }}>
    @if ($image)
        <img
            src="{{ $image->urlFor('hero') }}"
            alt="{{ $alt !== '' ? $alt : $title }}"
            class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.04]"
            loading="lazy"
            decoding="async"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-charcoal/55 via-charcoal/10 to-transparent"></div>
        <div class="relative flex h-full min-h-[280px] flex-col justify-end p-6 text-ivory md:min-h-[420px] md:p-8">
            <h3 class="font-display text-3xl md:text-4xl">{{ $title }}</h3>
            <span class="mt-3 text-[11px] uppercase tracking-[0.18em]">Shop now</span>
        </div>
    @else
        <div class="relative flex h-full min-h-[280px] flex-col justify-end bg-ivory-dark p-6 md:min-h-[420px] md:p-8">
            <h3 class="font-display text-3xl text-charcoal md:text-4xl">{{ $title }}</h3>
            <span class="mt-3 text-[11px] uppercase tracking-[0.18em] text-muted">Shop now</span>
        </div>
    @endif
</a>
