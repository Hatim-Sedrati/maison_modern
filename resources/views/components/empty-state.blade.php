@props([
    'title',
    'text' => null,
    'href' => null,
    'action' => null,
])

<div class="px-4 py-20 text-center md:py-28">
    <h2 class="font-display text-3xl tracking-wide text-ink md:text-4xl">{{ $title }}</h2>
    @if ($text)
        <p class="mx-auto mt-4 max-w-md text-sm leading-relaxed text-charcoal-light">{{ $text }}</p>
    @endif
    @if ($href && $action)
        <a href="{{ $href }}" class="btn-primary mt-8">{{ $action }}</a>
    @endif
</div>
