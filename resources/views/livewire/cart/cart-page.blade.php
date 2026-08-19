@php
    use App\Support\Money;
@endphp

<div>
    @if ($items->isEmpty())
        <x-empty-state
            title="Your cart is empty"
            text="Discover our latest collection."
            :href="route('shop.index')"
            action="Shop now"
        />
    @else
        @if ($error)
            <p class="mb-6 text-sm text-red-800" role="alert">{{ $error }}</p>
        @endif

        <div class="grid gap-12 lg:grid-cols-[minmax(0,1.4fr)_minmax(280px,0.8fr)]">
            <ul class="divide-y divide-ivory-dark border-y border-ivory-dark" role="list">
                @foreach ($items as $item)
                    @php
                        $product = $item['product'];
                        $variant = $item['variant'];
                        $image = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                        $max = $product->availableStock($variant);
                    @endphp
                    <li class="flex gap-4 py-6" wire:key="{{ $item['key'] }}">
                        <a href="{{ route('product.show', $product) }}" class="w-24 shrink-0 bg-ivory-dark md:w-28">
                            @if ($image)
                                <img src="{{ $image->url }}" alt="{{ $product->name }}" class="aspect-[3/4] w-full object-cover" loading="lazy">
                            @else
                                <div class="aspect-[3/4] flex items-center justify-center">
                                    <span class="sr-only">{{ $product->name }}</span>
                                </div>
                            @endif
                        </a>

                        <div class="flex min-w-0 flex-1 flex-col">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <a href="{{ route('product.show', $product) }}" class="text-sm">{{ $product->name }}</a>
                                    @if ($variant?->color)
                                        <p class="mt-1 text-xs text-muted">Color: {{ $variant->color }}</p>
                                    @endif
                                    @if ($variant?->size)
                                        <p class="text-xs text-muted">Size: {{ $variant->size }}</p>
                                    @endif
                                    <p class="mt-2 text-sm">{{ Money::format($item['unit_price']) }}</p>
                                </div>
                                <button
                                    type="button"
                                    wire:click="remove('{{ $item['key'] }}')"
                                    class="text-xs uppercase tracking-widest text-muted hover:text-charcoal"
                                >
                                    Remove
                                </button>
                            </div>

                            <div class="mt-4 flex items-center justify-between gap-4">
                                <div class="flex items-center border border-ivory-dark" role="group" aria-label="Quantity for {{ $product->name }}">
                                    <button
                                        type="button"
                                        wire:click="updateQuantity('{{ $item['key'] }}', {{ $item['quantity'] - 1 }})"
                                        class="flex h-11 w-11 items-center justify-center"
                                        aria-label="Decrease quantity"
                                    >−</button>
                                    <span class="min-w-6 text-center text-sm">{{ $item['quantity'] }}</span>
                                    <button
                                        type="button"
                                        wire:click="updateQuantity('{{ $item['key'] }}', {{ $item['quantity'] + 1 }})"
                                        class="flex h-11 w-11 items-center justify-center"
                                        aria-label="Increase quantity"
                                        @disabled($item['quantity'] >= $max)
                                    >+</button>
                                </div>
                                <p class="text-sm">{{ Money::format($item['line_total']) }}</p>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <aside class="h-fit border border-ivory-dark bg-white/40 p-6">
                <h2 class="text-xs uppercase tracking-widest">Summary</h2>
                <dl class="mt-6 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt>Subtotal</dt>
                        <dd>{{ Money::format($subtotal) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>Delivery</dt>
                        <dd>{{ Money::isZero($deliveryFee) ? 'Free' : Money::format($deliveryFee) }}</dd>
                    </div>
                    <div class="flex justify-between border-t border-ivory-dark pt-3 text-base">
                        <dt>Total</dt>
                        <dd>{{ Money::format($total) }}</dd>
                    </div>
                </dl>

                <p class="mt-4 text-xs leading-relaxed text-muted">Cash on delivery. Totals are confirmed when you place your order.</p>

                <a href="{{ route('checkout.index') }}" class="btn-primary mt-6 w-full">Proceed to checkout</a>
                <a href="{{ route('shop.index') }}" class="btn-secondary mt-3 w-full">Continue shopping</a>
            </aside>
        </div>
    @endif
</div>
