<?php

namespace Tests\Feature;

use App\Exceptions\CartException;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_product_cannot_be_added_to_cart(): void
    {
        $this->expectException(CartException::class);

        app(Cart::class)->add(999999, null, 1);
    }

    public function test_invalid_variant_cannot_be_added_to_cart(): void
    {
        $product = Product::factory()->create(['stock' => 5]);

        $this->expectException(CartException::class);

        app(Cart::class)->add($product->id, 999999, 1);
    }

    public function test_inactive_product_cannot_be_added_to_cart(): void
    {
        $product = Product::factory()->inactive()->create(['stock' => 5]);

        $this->expectException(CartException::class);

        app(Cart::class)->add($product->id, null, 1);
    }

    public function test_cart_can_be_cleared(): void
    {
        $product = Product::factory()->create(['stock' => 5]);
        $cart = app(Cart::class);
        $cart->add($product->id, null, 1);

        $cart->clear();

        $this->assertTrue($cart->isEmpty());
        $this->assertSame(0, $cart->quantity());
    }

    public function test_deleted_products_are_removed_from_the_cart_gracefully(): void
    {
        $product = Product::factory()->create(['stock' => 5]);
        $cart = app(Cart::class);
        $cart->add($product->id, null, 1);

        $product->delete();

        $this->assertTrue($cart->items()->isEmpty());
        $this->assertTrue($cart->isEmpty());
    }

    public function test_deleted_variants_are_removed_from_the_cart_gracefully(): void
    {
        $product = Product::factory()->create(['stock' => 0]);
        $variant = ProductVariant::factory()->for($product)->create(['stock' => 3]);
        $cart = app(Cart::class);
        $cart->add($product->id, $variant->id, 1);

        $variant->delete();

        $this->assertTrue($cart->items()->isEmpty());
    }
}
