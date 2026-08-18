<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Cart;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CheckoutTest extends TestCase
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
            'email' => 'amina@example.com',
        ];
    }

    public function test_order_can_be_created_from_valid_cart_data(): void
    {
        $product = Product::factory()->create(['stock' => 5, 'price' => '120.00']);
        app(Cart::class)->add($product->id, null, 2);

        $order = app(OrderService::class)->createFromCart($this->customerPayload());

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'customer_name' => 'Amina El Fassi',
            'phone' => '0612345678',
        ]);
        $this->assertSame(PaymentMethod::CashOnDelivery, $order->payment_method);
        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertTrue(app(Cart::class)->isEmpty());
    }

    public function test_order_totals_are_calculated_server_side(): void
    {
        Config::set('shop.delivery_fee', '30.00');

        $product = Product::factory()->create(['stock' => 5, 'price' => '100.00']);
        app(Cart::class)->add($product->id, null, 2);

        $order = app(OrderService::class)->createFromCart($this->customerPayload() + [
            'total' => '1.00',
            'subtotal' => '1.00',
            'delivery_fee' => '0.00',
        ]);

        $this->assertSame('200.00', $order->subtotal);
        $this->assertSame('30.00', $order->delivery_fee);
        $this->assertSame('230.00', $order->total);
    }

    public function test_stock_decreases_correctly_after_order_creation(): void
    {
        $product = Product::factory()->create(['stock' => 5]);
        $variant = ProductVariant::factory()->for($product)->create(['stock' => 4]);

        app(Cart::class)->add($product->id, $variant->id, 2);
        app(OrderService::class)->createFromCart($this->customerPayload());

        $this->assertSame(2, $variant->fresh()->stock);
        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_order_creation_is_transactional(): void
    {
        $available = Product::factory()->create(['stock' => 5, 'price' => '50.00']);
        $unavailable = Product::factory()->create(['stock' => 1, 'price' => '50.00']);

        $cart = app(Cart::class);
        $cart->add($available->id, null, 1);
        $cart->add($unavailable->id, null, 1);

        $unavailable->update(['stock' => 0]);

        try {
            app(OrderService::class)->createFromCart($this->customerPayload());
            $this->fail('Order creation should have failed.');
        } catch (InsufficientStockException) {
            // expected
        }

        $this->assertSame(0, Order::query()->count());
        $this->assertSame(5, $available->fresh()->stock);
        $this->assertSame(0, $unavailable->fresh()->stock);
        $this->assertFalse($cart->isEmpty());
    }

    public function test_historical_order_item_information_is_preserved(): void
    {
        $product = Product::factory()->create([
            'name' => 'Maison Tee',
            'sku' => 'MM-TEE-001',
            'price' => '199.00',
            'stock' => 5,
        ]);

        app(Cart::class)->add($product->id, null, 1);
        $order = app(OrderService::class)->createFromCart($this->customerPayload());

        $product->update([
            'name' => 'Renamed Tee',
            'sku' => 'MM-TEE-999',
            'price' => '9.00',
        ]);

        $item = $order->items()->first();

        $this->assertSame('Maison Tee', $item->product_name);
        $this->assertSame('MM-TEE-001', $item->sku);
        $this->assertSame('199.00', $item->unit_price);
        $this->assertSame('199.00', $item->total);
    }

    public function test_customer_authentication_is_not_required_to_place_an_order(): void
    {
        $this->assertGuest();

        $product = Product::factory()->create(['stock' => 2, 'price' => '80.00']);
        app(Cart::class)->add($product->id, null, 1);

        $order = app(OrderService::class)->createFromCart($this->customerPayload());

        $this->assertNotNull($order->id);
        $this->assertGuest();
    }

    public function test_checkout_validates_customer_information(): void
    {
        $validator = Validator::make(
            ['customer_name' => '', 'phone' => '', 'city' => '', 'address' => ''],
            (new \App\Http\Requests\StoreOrderRequest)->rules(),
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('customer_name', $validator->errors()->toArray());
        $this->assertArrayHasKey('phone', $validator->errors()->toArray());
        $this->assertArrayHasKey('city', $validator->errors()->toArray());
        $this->assertArrayHasKey('address', $validator->errors()->toArray());
    }

    public function test_empty_cart_cannot_create_an_order(): void
    {
        $this->expectException(ValidationException::class);

        app(OrderService::class)->createFromCart($this->customerPayload());
    }
}
