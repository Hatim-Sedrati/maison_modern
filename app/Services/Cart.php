<?php

namespace App\Services;

use App\Exceptions\CartException;
use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Support\Money;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Collection;

class Cart
{
    public const SESSION_KEY = 'cart';

    protected bool $pruned = false;

    public function __construct(protected SessionManager $session) {}

    /**
     * @return array<string, array{product_id: int, variant_id: int|null, quantity: int}>
     */
    public function raw(): array
    {
        /** @var array<string, array{product_id: int, variant_id: int|null, quantity: int}> $items */
        $items = $this->session->get(self::SESSION_KEY, []);

        return $items;
    }

    public function add(int $productId, ?int $variantId = null, int $quantity = 1): void
    {
        if ($quantity < 1) {
            throw new CartException('Quantity must be at least 1.');
        }

        [$product, $variant] = $this->resolvePurchasable($productId, $variantId);

        $key = $this->lineKey($productId, $variantId);
        $items = $this->raw();
        $newQuantity = ($items[$key]['quantity'] ?? 0) + $quantity;
        $stock = $product->availableStock($variant);

        if ($newQuantity > $stock) {
            throw new InsufficientStockException('Not enough stock for this item.');
        }

        $items[$key] = [
            'product_id' => $productId,
            'variant_id' => $variantId,
            'quantity' => $newQuantity,
        ];

        $this->save($items);
    }

    public function update(string $lineKey, int $quantity): void
    {
        $items = $this->raw();

        if (! isset($items[$lineKey])) {
            throw new CartException('This cart item does not exist.');
        }

        if ($quantity < 1) {
            unset($items[$lineKey]);
            $this->save($items);

            return;
        }

        $line = $items[$lineKey];
        [$product, $variant] = $this->resolvePurchasable(
            (int) $line['product_id'],
            $line['variant_id'] !== null ? (int) $line['variant_id'] : null,
        );

        if ($quantity > $product->availableStock($variant)) {
            throw new InsufficientStockException('Not enough stock for this item.');
        }

        $items[$lineKey]['quantity'] = $quantity;
        $this->save($items);
    }

    public function remove(string $lineKey): void
    {
        $items = $this->raw();
        unset($items[$lineKey]);
        $this->save($items);
    }

    public function clear(): void
    {
        $this->session->forget(self::SESSION_KEY);
    }

    public function isEmpty(): bool
    {
        $this->pruneInvalid();

        return $this->raw() === [];
    }

    public function quantity(): int
    {
        $this->pruneInvalid();

        return (int) collect($this->raw())->sum('quantity');
    }

    /**
     * @return Collection<int, array{
     *     key: string,
     *     product: Product,
     *     variant: ProductVariant|null,
     *     quantity: int,
     *     unit_price: string,
     *     line_total: string
     * }>
     */
    public function items(): Collection
    {
        $this->pruneInvalid();

        $raw = $this->raw();
        $productIds = collect($raw)->pluck('product_id')->unique()->filter()->all();
        $products = Product::query()
            ->with(['variants', 'images', 'category'])
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        return collect($raw)
            ->map(function (array $line, string $key) use ($products): ?array {
                $product = $products->get($line['product_id']);

                if (! $product || ! $product->is_active) {
                    return null;
                }

                $variant = null;

                if ($line['variant_id']) {
                    $variant = $product->variants->firstWhere('id', (int) $line['variant_id']);

                    if (! $variant || ! $variant->is_active) {
                        return null;
                    }
                } elseif ($product->hasActiveVariants()) {
                    return null;
                }

                $unitPrice = $product->unitPrice($variant);

                return [
                    'key' => $key,
                    'product' => $product,
                    'variant' => $variant,
                    'quantity' => (int) $line['quantity'],
                    'unit_price' => $unitPrice,
                    'line_total' => Money::multiply($unitPrice, (int) $line['quantity']),
                ];
            })
            ->filter()
            ->values();
    }

    public function subtotal(): string
    {
        return $this->items()->reduce(
            fn (string $carry, array $item): string => Money::add($carry, $item['line_total']),
            '0.00',
        );
    }

    public function lineKey(int $productId, ?int $variantId): string
    {
        return sha1($productId.'-'.($variantId ?? '0'));
    }

    /**
     * Drop session lines that can no longer be purchased.
     */
    public function pruneInvalid(): void
    {
        if ($this->pruned) {
            return;
        }

        $this->pruned = true;

        $raw = $this->raw();
        $kept = [];

        foreach ($raw as $key => $line) {
            try {
                $this->resolvePurchasable(
                    (int) $line['product_id'],
                    isset($line['variant_id']) && $line['variant_id'] !== null ? (int) $line['variant_id'] : null,
                );
                $kept[$key] = $line;
            } catch (CartException) {
                continue;
            }
        }

        if (count($kept) !== count($raw)) {
            $this->save($kept);
        }
    }

    /**
     * @return array{0: Product, 1: ProductVariant|null}
     */
    protected function resolvePurchasable(int $productId, ?int $variantId): array
    {
        $product = Product::query()->with('variants')->find($productId);

        if (! $product || ! $product->is_active) {
            throw new CartException('This product is not available.');
        }

        $variant = null;

        if ($variantId !== null) {
            $variant = $product->variants->firstWhere('id', $variantId);

            if (! $variant || ! $variant->is_active) {
                throw new CartException('This variant is not available.');
            }
        } elseif ($product->hasActiveVariants()) {
            throw new CartException('Please select a product variant.');
        }

        return [$product, $variant];
    }

    /**
     * @param  array<string, array{product_id: int, variant_id: int|null, quantity: int}>  $items
     */
    protected function save(array $items): void
    {
        $this->session->put(self::SESSION_KEY, $items);
    }
}
