<?php

namespace Database\Factories;

use App\Enums\ProductGender;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->unique()->words(3, true),
            'slug' => null,
            'description' => fake()->paragraph(),
            'short_description' => fake()->sentence(),
            'gender' => fake()->randomElement(ProductGender::cases()),
            'price' => fake()->randomFloat(2, 50, 800),
            'compare_at_price' => null,
            'sku' => strtoupper(fake()->unique()->bothify('MM-####??')),
            'stock' => 10,
            'is_active' => true,
            'is_featured' => false,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_featured' => true,
        ]);
    }
}
