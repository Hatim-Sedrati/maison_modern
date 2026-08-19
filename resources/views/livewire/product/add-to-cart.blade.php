@php
    use App\Support\ColorSwatch;

    $colors = $this->availableColors();
    $sizes = $this->availableSizes();
    $stock = $this->maxQuantity();
@endphp

<div class="mt-8">
    <p class="text-lg">
        <x-price :amount="$this->currentPrice()" :compare="$product->compare_at_price" />
    </p>

    @if ($product->variants->isNotEmpty() && ! $this->usesLabeledOptions())
        <fieldset class="mt-8">
            <legend class="mb-3 text-[11px] uppercase tracking-[0.16em]">Options</legend>
            <div class="flex flex-wrap gap-2">
                @foreach ($product->variants as $variant)
                    <button
                        type="button"
                        wire:click="selectVariant({{ $variant->id }})"
                        @disabled($variant->stock < 1)
                        @class([
                            'min-h-11 min-w-11 border px-3 text-sm transition-colors',
                            'border-charcoal bg-charcoal text-ivory' => $variantId === $variant->id,
                            'border-sand hover:border-charcoal' => $variantId !== $variant->id && $variant->stock > 0,
                            'cursor-not-allowed border-sand text-muted line-through' => $variant->stock < 1,
                        ])
                    >
                        {{ $variant->label() }}
                    </button>
                @endforeach
            </div>
        </fieldset>
    @endif

    @if ($colors !== [])
        <fieldset class="mt-8">
            <legend class="mb-3 text-[11px] uppercase tracking-[0.16em]">
                Color @if ($selectedColor)<span class="text-muted">— {{ $selectedColor }}</span>@endif
            </legend>
            <div class="flex flex-wrap gap-3">
                @foreach ($colors as $color)
                    @php $available = $this->isColorAvailable($color); @endphp
                    <button
                        type="button"
                        wire:click="selectColor('{{ $color }}')"
                        class="relative h-9 w-9 rounded-full border-2 {{ $selectedColor === $color ? 'border-charcoal' : 'border-transparent' }} {{ $available ? '' : 'opacity-40' }}"
                        style="background-color: {{ ColorSwatch::hex($color) }}; box-shadow: inset 0 0 0 1px rgba(44,42,40,.15);"
                        aria-label="{{ $color }}{{ $available ? '' : ' (unavailable)' }}"
                        aria-pressed="{{ $selectedColor === $color ? 'true' : 'false' }}"
                    >
                        @unless ($available)
                            <span class="absolute inset-0 flex items-center justify-center text-[10px] text-charcoal" aria-hidden="true">×</span>
                        @endunless
                    </button>
                @endforeach
            </div>
        </fieldset>
    @endif

    @if ($sizes !== [])
        <fieldset class="mt-8">
            <legend class="mb-3 text-[11px] uppercase tracking-[0.16em]">Size</legend>
            <div class="flex flex-wrap gap-2">
                @foreach ($sizes as $size)
                    @php $available = $this->isSizeAvailable($size); @endphp
                    <button
                        type="button"
                        wire:click="selectSize('{{ $size }}')"
                        @class([
                            'min-h-11 min-w-11 border px-3 text-sm transition-colors',
                            'border-charcoal bg-charcoal text-ivory' => $selectedSize === $size && $available,
                            'border-charcoal' => $selectedSize === $size && ! $available,
                            'border-sand hover:border-charcoal' => $selectedSize !== $size && $available,
                            'cursor-not-allowed border-sand text-muted line-through' => ! $available,
                        ])
                        @disabled(! $available && $selectedSize !== $size)
                        aria-pressed="{{ $selectedSize === $size ? 'true' : 'false' }}"
                    >
                        {{ $size }}
                    </button>
                @endforeach
            </div>
        </fieldset>
    @endif

    <div class="mt-8">
        @if ($this->isAvailable())
            <p class="text-[11px] uppercase tracking-[0.16em] text-muted">
                @if ($stock <= 3)
                    Only {{ $stock }} left
                @else
                    In stock
                @endif
            </p>
        @else
            <p class="text-[11px] uppercase tracking-[0.16em] text-muted">This product is currently unavailable.</p>
        @endif
    </div>

    <div class="mt-6 flex items-center gap-4">
        <div class="flex items-center border border-sand" role="group" aria-label="Quantity">
            <button
                type="button"
                wire:click="decrementQuantity"
                class="flex h-12 w-12 items-center justify-center text-lg"
                aria-label="Decrease quantity"
                @disabled($quantity <= 1)
            >−</button>
            <span class="min-w-8 text-center text-sm" aria-live="polite">{{ $quantity }}</span>
            <button
                type="button"
                wire:click="incrementQuantity"
                class="flex h-12 w-12 items-center justify-center text-lg"
                aria-label="Increase quantity"
                @disabled($quantity >= $stock || $stock < 1)
            >+</button>
        </div>
    </div>

    <button
        type="button"
        wire:click="addToCart"
        class="btn-primary mt-6 w-full md:w-auto md:min-w-[240px]"
        @disabled(! $this->isAvailable())
        wire:loading.attr="disabled"
    >
        <span wire:loading.remove wire:target="addToCart">Add to cart</span>
        <span wire:loading wire:target="addToCart">Adding…</span>
    </button>

    @if ($message)
        <p
            class="mt-4 text-sm {{ $messageType === 'error' ? 'text-red-800' : 'text-charcoal-light' }}"
            role="{{ $messageType === 'error' ? 'alert' : 'status' }}"
        >
            {{ $message }}
            @if ($messageType === 'success')
                <a href="{{ route('cart.index') }}" class="ml-2 underline underline-offset-4">View cart</a>
            @endif
        </p>
    @endif
</div>
