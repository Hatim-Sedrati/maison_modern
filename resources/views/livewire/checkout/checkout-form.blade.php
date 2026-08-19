@php
    use App\Support\Money;
@endphp

<div>
    @if ($items->isEmpty())
        <x-empty-state
            title="Your cart is empty"
            text="Add something you like before checking out."
            :href="route('shop.index')"
            action="Shop now"
        />
    @else
        <div class="grid gap-12 lg:grid-cols-[minmax(0,1fr)_minmax(300px,380px)]">
            <form wire:submit="placeOrder" class="space-y-6">
                @error('cart')
                    <p class="text-sm text-red-800" role="alert">{{ $message }}</p>
                @enderror

                <div>
                    <label for="customer_name" class="mb-2 block text-xs uppercase tracking-widest">Full name</label>
                    <input id="customer_name" type="text" wire:model="customer_name" autocomplete="name" required class="input-field">
                    @error('customer_name') <p class="mt-1 text-sm text-red-800">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="mb-2 block text-xs uppercase tracking-widest">Phone number</label>
                    <input id="phone" type="tel" wire:model="phone" autocomplete="tel" inputmode="tel" required class="input-field">
                    @error('phone') <p class="mt-1 text-sm text-red-800">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="city" class="mb-2 block text-xs uppercase tracking-widest">City</label>
                    <input id="city" type="text" wire:model="city" autocomplete="address-level2" required class="input-field">
                    @error('city') <p class="mt-1 text-sm text-red-800">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="address" class="mb-2 block text-xs uppercase tracking-widest">Full address</label>
                    <textarea id="address" wire:model="address" autocomplete="street-address" rows="3" required class="input-field"></textarea>
                    @error('address') <p class="mt-1 text-sm text-red-800">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="mb-2 block text-xs uppercase tracking-widest">Email <span class="text-muted">(optional)</span></label>
                    <input id="email" type="email" wire:model="email" autocomplete="email" class="input-field">
                    @error('email') <p class="mt-1 text-sm text-red-800">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="postal_code" class="mb-2 block text-xs uppercase tracking-widest">Postal code <span class="text-muted">(optional)</span></label>
                    <input id="postal_code" type="text" wire:model="postal_code" autocomplete="postal-code" class="input-field">
                    @error('postal_code') <p class="mt-1 text-sm text-red-800">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="notes" class="mb-2 block text-xs uppercase tracking-widest">Order notes <span class="text-muted">(optional)</span></label>
                    <textarea id="notes" wire:model="notes" rows="3" class="input-field"></textarea>
                    @error('notes') <p class="mt-1 text-sm text-red-800">{{ $message }}</p> @enderror
                </div>

                <div class="border border-ivory-dark bg-ivory-dark/40 p-4">
                    <p class="text-xs uppercase tracking-widest">Payment</p>
                    <p class="mt-2 text-sm">Cash on Delivery</p>
                    <p class="mt-2 text-xs leading-relaxed text-muted">You pay when you receive your order. No online payment is required.</p>
                </div>

                <button type="submit" class="btn-primary w-full" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="placeOrder">Place order</span>
                    <span wire:loading wire:target="placeOrder">Placing order…</span>
                </button>
            </form>

            <aside class="h-fit border border-ivory-dark bg-white/40 p-6">
                <h2 class="text-xs uppercase tracking-widest">Your order</h2>
                <ul class="mt-6 divide-y divide-ivory-dark" role="list">
                    @foreach ($items as $item)
                        <li class="flex justify-between gap-4 py-3 text-sm">
                            <div>
                                <p>{{ $item['product']->name }}</p>
                                <p class="text-xs text-muted">
                                    Qty {{ $item['quantity'] }}
                                    @if ($item['variant']?->color) · {{ $item['variant']->color }} @endif
                                    @if ($item['variant']?->size) · {{ $item['variant']->size }} @endif
                                </p>
                            </div>
                            <p class="shrink-0">{{ Money::format($item['line_total']) }}</p>
                        </li>
                    @endforeach
                </ul>
                <dl class="mt-4 space-y-2 border-t border-ivory-dark pt-4 text-sm">
                    <div class="flex justify-between">
                        <dt>Subtotal</dt>
                        <dd>{{ Money::format($subtotal) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>Delivery</dt>
                        <dd>{{ Money::isZero($deliveryFee) ? 'Free' : Money::format($deliveryFee) }}</dd>
                    </div>
                    <div class="flex justify-between pt-2 text-base">
                        <dt>Total</dt>
                        <dd>{{ Money::format($total) }}</dd>
                    </div>
                </dl>
            </aside>
        </div>
    @endif
</div>
