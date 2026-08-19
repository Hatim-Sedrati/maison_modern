@props([
    'title',
    'text' => null,
    'href' => null,
    'action' => null,
])

<div class="py-16 text-center">
    <h2 class="section-title">{{ $title }}</h2>
    @if ($text)
        <p class="mx-auto mt-4 max-w-md text-sm leading-relaxed text-muted">{{ $text }}</p>
    @endif
    @if ($href && $action)
        <a href="{{ $href }}" class="btn-primary mt-8">{{ $action }}</a>
    @endif
</div>
