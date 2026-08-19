<?php

namespace App\Livewire\Product;

use App\Exceptions\CartException;
use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Cart;
use Livewire\Component;

class AddToCart extends Component
{
    public Product $product;

    public ?int $variantId = null;

    public ?string $selectedColor = null;

    public ?string $selectedSize = null;

    public int $quantity = 1;

    public ?string $message = null;

    public string $messageType = 'success';

    public function mount(Product $product): void
    {
        $this->product = $product->load(['variants' => fn ($q) => $q->active()]);
        $this->initializeSelection();
    }

    public function selectColor(string $color): void
    {
        $this->selectedColor = $color;
        $this->message = null;

        $byColor = $this->product->variants->where('color', $color);
        $match = $byColor->first(
            fn (ProductVariant $variant): bool => $variant->size === $this->selectedSize && $variant->stock > 0,
        )
            ?? $byColor->first(fn (ProductVariant $variant): bool => $variant->size === $this->selectedSize)
            ?? $byColor->first(fn (ProductVariant $variant): bool => $variant->stock > 0)
            ?? $byColor->first();

        $this->selectedSize = $match?->size ?? $this->selectedSize;
        $this->variantId = $match?->id;
        $this->quantity = 1;
    }

    public function selectSize(string $size): void
    {
        $this->selectedSize = $size;
        $this->message = null;

        $bySize = $this->product->variants->where('size', $size);
        $match = $bySize->first(
            fn (ProductVariant $variant): bool => $variant->color === $this->selectedColor && $variant->stock > 0,
        )
            ?? $bySize->first(fn (ProductVariant $variant): bool => $variant->color === $this->selectedColor)
            ?? $bySize->first(fn (ProductVariant $variant): bool => $variant->stock > 0)
            ?? $bySize->first();

        $this->selectedColor = $match?->color ?? $this->selectedColor;
        $this->variantId = $match?->id;
        $this->quantity = 1;
    }

    public function selectVariant(int $variantId): void
    {
        $variant = $this->product->variants->firstWhere('id', $variantId);

        if (! $variant) {
            return;
        }

        $this->variantId = $variant->id;
        $this->selectedColor = $variant->color;
        $this->selectedSize = $variant->size;
        $this->quantity = 1;
        $this->message = null;
    }

    public function incrementQuantity(): void
    {
        if ($this->quantity < $this->maxQuantity()) {
            $this->quantity++;
        }
    }

    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart(Cart $cart): void
    {
        $this->message = null;

        if ($this->product->hasActiveVariants() && $this->variantId === null) {
            $this->message = 'Please select a product option.';
            $this->messageType = 'error';

            return;
        }

        if (! $this->isAvailable()) {
            $this->message = 'This product is currently unavailable.';
            $this->messageType = 'error';

            return;
        }

        try {
            $cart->add($this->product->id, $this->variantId, $this->quantity);
            $this->message = 'Added to cart';
            $this->messageType = 'success';
            $this->dispatch('cart-updated');
        } catch (InsufficientStockException $e) {
            $this->message = $e->getMessage();
            $this->messageType = 'error';
        } catch (CartException $e) {
            $this->message = $e->getMessage();
            $this->messageType = 'error';
        }
    }

    public function selectedVariant(): ?ProductVariant
    {
        if (! $this->variantId) {
            return null;
        }

        return $this->product->variants->firstWhere('id', $this->variantId);
    }

    public function currentPrice(): string
    {
        return $this->product->unitPrice($this->selectedVariant());
    }

    public function maxQuantity(): int
    {
        return max(0, $this->product->availableStock($this->selectedVariant()));
    }

    public function isAvailable(): bool
    {
        return $this->product->availableStock($this->selectedVariant()) > 0;
    }

    /** @return array<int, string> */
    public function availableSizes(): array
    {
        $order = ['XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];

        return $this->product->variants
            ->pluck('size')
            ->filter()
            ->unique()
            ->sortBy(function (string $size) use ($order): int {
                $index = array_search(strtoupper($size), $order, true);

                return $index === false ? 100 : $index;
            })
            ->values()
            ->all();
    }

    /** @return array<int, string> */
    public function availableColors(): array
    {
        return $this->product->variants
            ->pluck('color')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function isColorAvailable(string $color): bool
    {
        return $this->product->variants
            ->where('color', $color)
            ->contains(fn (ProductVariant $variant): bool => $variant->stock > 0);
    }

    public function isSizeAvailable(string $size): bool
    {
        return $this->product->variants
            ->filter(fn (ProductVariant $variant): bool => $variant->size === $size)
            ->when(
                $this->selectedColor,
                fn ($collection) => $collection->where('color', $this->selectedColor),
            )
            ->contains(fn (ProductVariant $variant): bool => $variant->stock > 0);
    }

    public function usesLabeledOptions(): bool
    {
        return $this->availableColors() !== [] || $this->availableSizes() !== [];
    }

    public function render()
    {
        return view('livewire.product.add-to-cart');
    }

    protected function initializeSelection(): void
    {
        $firstAvailable = $this->product->variants->first(
            fn (ProductVariant $variant): bool => $variant->stock > 0,
        ) ?? $this->product->variants->first();

        if (! $firstAvailable) {
            return;
        }

        $this->variantId = $firstAvailable->id;
        $this->selectedColor = $firstAvailable->color;
        $this->selectedSize = $firstAvailable->size;
    }
}
