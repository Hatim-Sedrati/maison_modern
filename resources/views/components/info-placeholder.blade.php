@props(['label' => 'To be completed'])

<aside {{ $attributes->merge(['class' => 'mt-5 border-l border-taupe/70 pl-5', 'role' => 'note']) }}>
    <p class="text-[10px] uppercase tracking-[0.18em] text-muted">{{ $label }}</p>
    <div class="mt-2 text-sm leading-relaxed text-charcoal">{{ $slot }}</div>
</aside>
