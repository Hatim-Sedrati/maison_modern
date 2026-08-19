@php
    use App\Support\Money;
@endphp

<div>
    @if ($items->isEmpty())
        <x-empty-state
            title="Your cart is empty"
            text="Discover something you'll love."
            :href="route('shop.new-arrivals')"
            action="Shop New Arrivals"
        />
    @else
        @if ($error)
            <p class="mb-6 text-sm text-red-800" role="alert">{{ $error }}</p>
        @endif

        <div class="grid gap-12 lg:grid-cols-[minmax(0,1.45fr)_minmax(280px,0.75fr)] lg:gap-16">
            <ul class="divide-y divide-sand border-y border-sand" role="list">
                @foreach ($items as $item)
                    @php
                        $product = $item['product'];
                        $variant = $item['variant'];
                        $image = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                        $max = $product->availableStock($variant);
                    @endphp
                    <li class="flex gap-4 py-6 md:gap-6" wire:key="{{ $item['key'] }}">
                        <a href="{{ route('product.show', $product) }}" class="w-24 shrink-0 bg-ivory-dark md:w-28">
                            @if ($image)
                                <img src="{{ $image->urlFor('thumb') }}" alt="{{ $product->name }}" class="aspect-[3/4] w-full object-cover" loading="lazy">
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
                                    <p class="mt-2 text-sm text-charcoal-light">{{ Money::format($item['unit_price']) }}</p>
                                    @if ($max < 1)
                                        <p class="mt-2 text-xs text-red-800">This item is currently unavailable.</p>
                                    @elseif ($item['quantity'] > $max)
                                        <p class="mt-2 text-xs text-red-800">Only {{ $max }} available.</p>
                                    @endif
                                </div>
                                <button
                                    type="button"
                                    wire:click="remove('{{ $item['key'] }}')"
                                    class="text-[11px] uppercase tracking-[0.16em] text-muted transition-colors hover:text-charcoal"
                                >
                                    Remove
                                </button>
                            </div>

                            <div class="mt-4 flex items-center justify-between gap-4">
                                <div class="flex items-center border border-sand" role="group" aria-label="Quantity for {{ $product->name }}">
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

            <aside class="h-fit bg-ivory p-6 md:p-8">
                <h2 class="text-[11px] uppercase tracking-[0.16em]">Summary</h2>
                <dl class="mt-6 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt>Subtotal</dt>
                        <dd>{{ Money::format($subtotal) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>Delivery</dt>
                        <dd>{{ Money::isZero($deliveryFee) ? 'Free' : Money::format($deliveryFee) }}</dd>
                    </div>
                    <div class="flex justify-between border-t border-sand pt-3 text-base">
                        <dt>Total</dt>
                        <dd>{{ Money::format($total) }}</dd>
                    </div>
                </dl>

                <p class="mt-4 text-xs leading-relaxed text-muted">Cash on Delivery. Totals are confirmed when you place your order.</p>

                <a href="{{ route('checkout.index') }}" class="btn-primary mt-6 w-full">Proceed to Checkout</a>
                <a href="{{ route('shop.index') }}" class="btn-ghost mt-3 w-full">Continue Shopping</a>
            </aside>
        </div>
    @endif
</div>
