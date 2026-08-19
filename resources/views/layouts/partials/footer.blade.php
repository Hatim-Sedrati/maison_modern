<footer class="mt-auto border-t border-ivory-dark bg-ivory-dark/40">
    <div class="mx-auto grid max-w-7xl gap-10 px-6 py-14 md:grid-cols-3">
        <div>
            <a href="{{ route('home') }}" class="inline-block">
                <img src="{{ asset('images/logo.png') }}" alt="Maison Modern" class="mb-5 h-8 w-auto max-w-[200px] object-contain">
            </a>
            <p class="max-w-xs text-sm leading-relaxed text-muted">
                Modern Moroccan fashion. Elegant, warm, and easy to wear.
            </p>
        </div>

        <div>
            <h2 class="mb-4 text-xs font-medium uppercase tracking-widest">Shop</h2>
            <ul class="space-y-3 text-sm text-charcoal-light">
                <li><a href="{{ route('shop.new-arrivals') }}" class="hover:text-charcoal">New Arrivals</a></li>
                <li><a href="{{ route('shop.women') }}" class="hover:text-charcoal">Women</a></li>
                <li><a href="{{ route('shop.men') }}" class="hover:text-charcoal">Men</a></li>
                <li><a href="{{ route('shop.accessories') }}" class="hover:text-charcoal">Accessories</a></li>
            </ul>
        </div>

        <div>
            <h2 class="mb-4 text-xs font-medium uppercase tracking-widest">Customer Care</h2>
            <ul class="space-y-3 text-sm text-charcoal-light">
                <li><a href="{{ route('pages.contact') }}" class="hover:text-charcoal">Contact</a></li>
                <li><a href="{{ route('pages.shipping') }}" class="hover:text-charcoal">Shipping</a></li>
                <li><a href="{{ route('pages.returns') }}" class="hover:text-charcoal">Returns</a></li>
                <li><a href="{{ route('pages.faq') }}" class="hover:text-charcoal">FAQ</a></li>
            </ul>
        </div>
    </div>

    <div class="border-t border-ivory-dark px-6 py-6 text-center text-xs tracking-wide text-muted">
        &copy; {{ date('Y') }} Maison Modern. All rights reserved.
    </div>
</footer>
