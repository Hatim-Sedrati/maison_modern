<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 3);
        $unitPrice = '99.00';

        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'product_variant_id' => null,
            'product_name' => fake()->words(3, true),
            'sku' => strtoupper(fake()->bothify('MM-####')),
            'selected_size' => null,
            'selected_color' => null,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total' => sprintf('%.2f', 99 * $quantity),
        ];
    }
}
