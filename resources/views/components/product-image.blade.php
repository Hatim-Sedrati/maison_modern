@props([
    'image' => null,
    'alt' => '',
    'preset' => 'gallery',
    'loading' => 'lazy',
])

@if ($image)
    <img
        src="{{ $image->urlFor($preset) }}"
        alt="{{ $alt }}"
        loading="{{ $loading }}"
        decoding="async"
        {{ $attributes }}
    >
@else
    <div {{ $attributes->merge(['class' => 'flex items-center justify-center bg-ivory-dark']) }}>
        <span class="px-4 text-center text-[10px] uppercase tracking-[0.25em] text-muted">Maison Modern</span>
    </div>
@endif
