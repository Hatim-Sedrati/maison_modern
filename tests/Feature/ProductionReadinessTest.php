<?php

namespace Tests\Feature;

use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\Product;
use App\Services\Cart;
use App\Services\OrderService;
use App\Support\OrderConfirmation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, string>
     */
    protected function customerPayload(): array
    {
        return [
            'customer_name' => 'Amina El Fassi',
            'phone' => '0612345678',
            'city' => 'Casablanca',
            'address' => '12 Rue des Fleurs',
        ];
    }

    public function test_homepage_includes_basic_seo_metadata(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Maison Modern — contemporary Moroccan fashion', false)
            ->assertSee('<link rel="canonical"', false)
            ->assertSee('<meta property="og:title"', false);
    }

    public function test_order_confirmation_requires_a_signature(): void
    {
        $order = Order::factory()->create();

        $this->get(route('order.confirmation', $order))->assertForbidden();

        $this->get(OrderConfirmation::url($order))
            ->assertOk()
            ->assertSee($order->order_number);
    }

    public function test_products_with_order_history_cannot_be_deleted(): void
    {
        $product = Product::factory()->create(['stock' => 2, 'price' => '80.00']);
        app(Cart::class)->add($product->id, null, 1);
        app(OrderService::class)->createFromCart($this->customerPayload());

        $this->assertFalse($product->delete());
        $this->assertNotNull($product->fresh());
    }

    public function test_categories_with_products_cannot_be_deleted(): void
    {
        $product = Product::factory()->create();

        $this->assertFalse($product->category->delete());
        $this->assertNotNull($product->category->fresh());
    }

    public function test_last_item_cannot_be_sold_twice(): void
    {
        $product = Product::factory()->create(['stock' => 1, 'price' => '90.00']);

        $first = app(Cart::class);
        $first->add($product->id, null, 1);
        app(OrderService::class)->createFromCart($this->customerPayload());

        $this->assertSame(0, $product->fresh()->stock);

        $this->app->forgetInstance(Cart::class);
        $second = app(Cart::class);

        try {
            $second->add($product->id, null, 1);
            $this->fail('The second customer should not have been able to add the last item.');
        } catch (InsufficientStockException) {
            // expected
        }

        $this->assertSame(1, Order::query()->count());
    }

    public function test_friendly_error_pages_are_branded(): void
    {
        $this->get('/definitely-missing-page')
            ->assertNotFound()
            ->assertSee('Page not found')
            ->assertSee('Maison Modern');
    }
}
