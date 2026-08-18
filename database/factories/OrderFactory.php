<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = '199.00';
        $deliveryFee = '0.00';

        return [
            'order_number' => 'MM-'.now()->format('Ymd').'-'.str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'customer_name' => fake()->name(),
            'phone' => '06'.fake()->numerify('########'),
            'email' => fake()->optional()->safeEmail(),
            'city' => fake()->city(),
            'address' => fake()->streetAddress(),
            'postal_code' => fake()->optional()->postcode(),
            'notes' => fake()->optional()->sentence(),
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total' => '199.00',
            'status' => OrderStatus::Pending,
            'payment_method' => PaymentMethod::CashOnDelivery,
        ];
    }
}
