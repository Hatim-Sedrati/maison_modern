@props(['title'])

<section>
    <h2 class="font-display text-2xl text-ink md:text-[1.65rem]">{{ $title }}</h2>
    <div class="mt-4 space-y-4 text-sm leading-relaxed text-charcoal-light md:text-[0.95rem]">
        {{ $slot }}
    </div>
</section>
