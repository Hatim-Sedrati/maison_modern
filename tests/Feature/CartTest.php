<?php

namespace Tests\Feature;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_can_add_a_product(): void
    {
        $product = Product::factory()->create(['stock' => 5, 'price' => '100.00']);

        $cart = app(Cart::class);
        $cart->add($product->id, null, 2);

        $this->assertSame(2, $cart->quantity());
        $this->assertSame('200.00', $cart->subtotal());
    }

    public function test_cart_can_add_a_variant(): void
    {
        $product = Product::factory()->create(['price' => '80.00', 'stock' => 0]);
        $variant = ProductVariant::factory()->for($product)->create([
            'stock' => 4,
            'price' => '90.00',
        ]);

        $cart = app(Cart::class);
        $cart->add($product->id, $variant->id, 1);

        $this->assertSame(1, $cart->quantity());
        $this->assertSame('90.00', $cart->subtotal());
        $this->assertSame($variant->id, $cart->items()->first()['variant']->id);
    }

    public function test_cart_can_update_quantity(): void
    {
        $product = Product::factory()->create(['stock' => 10, 'price' => '50.00']);
        $cart = app(Cart::class);
        $cart->add($product->id, null, 1);
        $key = $cart->items()->first()['key'];

        $cart->update($key, 3);

        $this->assertSame(3, $cart->quantity());
        $this->assertSame('150.00', $cart->subtotal());
    }

    public function test_cart_can_remove_an_item(): void
    {
        $product = Product::factory()->create(['stock' => 5]);
        $cart = app(Cart::class);
        $cart->add($product->id, null, 1);
        $key = $cart->items()->first()['key'];

        $cart->remove($key);

        $this->assertTrue($cart->isEmpty());
        $this->assertSame(0, $cart->quantity());
    }

    public function test_cart_calculates_totals_correctly(): void
    {
        $first = Product::factory()->create(['stock' => 5, 'price' => '100.00']);
        $second = Product::factory()->create(['stock' => 5, 'price' => '25.50']);

        $cart = app(Cart::class);
        $cart->add($first->id, null, 2);
        $cart->add($second->id, null, 1);

        $this->assertSame(3, $cart->quantity());
        $this->assertSame('225.50', $cart->subtotal());
    }

    public function test_cart_rejects_insufficient_stock(): void
    {
        $product = Product::factory()->create(['stock' => 1]);

        $this->expectException(InsufficientStockException::class);

        app(Cart::class)->add($product->id, null, 2);
    }

    public function test_customers_can_use_the_cart_without_authentication(): void
    {
        $this->assertGuest();

        $product = Product::factory()->create(['stock' => 3]);
        app(Cart::class)->add($product->id, null, 1);

        $this->assertSame(1, app(Cart::class)->quantity());
    }
}
