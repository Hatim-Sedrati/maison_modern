<footer class="mt-auto bg-ivory">
    <div class="page-shell grid gap-12 py-16 md:grid-cols-3 md:gap-10 md:py-20">
        <div>
            <a href="{{ route('home') }}" class="inline-block">
                <img src="{{ asset('images/logo.png') }}" alt="Maison Modern" class="mb-5 h-8 w-auto max-w-[200px] object-contain">
            </a>
            <p class="max-w-xs text-sm leading-relaxed text-charcoal-light">
                Modern Moroccan fashion. Warm, considered, and easy to wear.
            </p>
        </div>

        <div>
            <h2 class="mb-5 text-[11px] font-medium uppercase tracking-[0.18em]">Shop</h2>
            <ul class="space-y-3 text-sm text-charcoal-light">
                <li><a href="{{ route('shop.new-arrivals') }}" class="transition-colors hover:text-charcoal">New Arrivals</a></li>
                <li><a href="{{ route('shop.women') }}" class="transition-colors hover:text-charcoal">Women</a></li>
                <li><a href="{{ route('shop.men') }}" class="transition-colors hover:text-charcoal">Men</a></li>
                <li><a href="{{ route('shop.accessories') }}" class="transition-colors hover:text-charcoal">Accessories</a></li>
            </ul>
        </div>

        <div>
            <h2 class="mb-5 text-[11px] font-medium uppercase tracking-[0.18em]">Information</h2>
            <ul class="space-y-3 text-sm text-charcoal-light">
                <li><a href="{{ route('pages.delivery') }}" class="transition-colors hover:text-charcoal">Delivery</a></li>
                <li><a href="{{ route('pages.cash-on-delivery') }}" class="transition-colors hover:text-charcoal">Cash on Delivery</a></li>
                <li><a href="{{ route('pages.contact') }}" class="transition-colors hover:text-charcoal">Contact</a></li>
                <li><a href="{{ route('pages.returns') }}" class="transition-colors hover:text-charcoal">Returns</a></li>
            </ul>
        </div>
    </div>

    <div class="border-t border-sand/80 px-6 py-5 text-center text-[11px] tracking-[0.12em] text-muted">
        &copy; {{ date('Y') }} Maison Modern. All rights reserved.
    </div>
</footer>
