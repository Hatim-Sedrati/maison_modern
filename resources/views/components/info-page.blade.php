@props([
    'kicker' => 'Information',
    'title',
    'intro' => null,
    'crumbs' => [],
])

<article class="mx-auto max-w-2xl px-5 py-16 md:px-6 md:py-24">
    @if ($crumbs !== [])
        <x-breadcrumbs class="mb-10" :items="$crumbs" />
    @endif

    <p class="text-[11px] uppercase tracking-[0.28em] text-muted">{{ $kicker }}</p>
    <h1 class="mt-4 font-display text-4xl leading-tight text-ink md:text-5xl">{{ $title }}</h1>

    @if ($intro)
        <p class="mt-6 max-w-xl text-base leading-relaxed text-charcoal-light">{{ $intro }}</p>
    @endif

    <div class="mt-14 space-y-14">
        {{ $slot }}
    </div>
</article>
