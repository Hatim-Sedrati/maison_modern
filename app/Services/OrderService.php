<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Exceptions\CartException;
use App\Exceptions\InsufficientStockException;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(protected Cart $cart) {}

    /**
     * @param  array<string, mixed>  $customerData
     */
    public function createFromCart(array $customerData): Order
    {
        $customer = Validator::make($customerData, (new StoreOrderRequest)->rules())->validate();

        if ($this->cart->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Your cart is empty.',
            ]);
        }

        return DB::transaction(function () use ($customer): Order {
            $subtotal = '0.00';
            $preparedItems = [];

            foreach ($this->cart->raw() as $line) {
                $quantity = (int) $line['quantity'];
                $product = Product::query()
                    ->whereKey($line['product_id'])
                    ->lockForUpdate()
                    ->first();

                if (! $product || ! $product->is_active) {
                    throw new CartException('A product in your cart is no longer available.');
                }

                $variant = null;

                if ($line['variant_id']) {
                    $variant = ProductVariant::query()
                        ->where('product_id', $product->id)
                        ->whereKey($line['variant_id'])
                        ->lockForUpdate()
                        ->first();

                    if (! $variant || ! $variant->is_active) {
                        throw new CartException('A product variant in your cart is no longer available.');
                    }
                } elseif ($product->variants()->where('is_active', true)->exists()) {
                    throw new CartException('Please select a product variant.');
                }

                $stock = $variant ? $variant->stock : $product->stock;

                if ($quantity > $stock) {
                    throw new InsufficientStockException('Not enough stock for '.$product->name.'.');
                }

                $unitPrice = $product->unitPrice($variant);
                $lineTotal = Money::multiply($unitPrice, $quantity);
                $subtotal = Money::add($subtotal, $lineTotal);

                $preparedItems[] = [
                    'product' => $product,
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ];
            }

            $deliveryFee = Money::of(config('shop.delivery_fee', '0.00'));
            $total = Money::add($subtotal, $deliveryFee);

            $order = Order::query()->create([
                'order_number' => Order::generateOrderNumber(),
                'customer_name' => $customer['customer_name'],
                'phone' => $customer['phone'],
                'email' => $customer['email'] ?? null,
                'city' => $customer['city'],
                'address' => $customer['address'],
                'postal_code' => $customer['postal_code'] ?? null,
                'notes' => $customer['notes'] ?? null,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'status' => OrderStatus::Pending,
                'payment_method' => PaymentMethod::CashOnDelivery,
            ]);

            foreach ($preparedItems as $item) {
                /** @var Product $product */
                $product = $item['product'];
                /** @var ProductVariant|null $variant */
                $variant = $item['variant'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'product_name' => $product->name,
                    'sku' => $variant?->sku ?? $product->sku,
                    'selected_size' => $variant?->size,
                    'selected_color' => $variant?->color,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['line_total'],
                ]);

                if ($variant) {
                    $variant->decrement('stock', $item['quantity']);
                } else {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            $this->cart->clear();

            return $order->load('items');
        });
    }
}
