<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\CloudinaryImageService;
use App\Support\WhatsAppOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class PhaseFourIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cloudinary_urls_use_configured_transformations(): void
    {
        config([
            'services.cloudinary.cloud_name' => 'maison-test',
            'services.cloudinary.url' => null,
            'services.cloudinary.api_key' => null,
            'services.cloudinary.api_secret' => null,
        ]);

        $image = ProductImage::factory()->create([
            'path' => 'https://res.cloudinary.com/maison-test/image/upload/v1/maison-modern/products/shirt.jpg',
            'public_id' => 'maison-modern/products/shirt',
        ]);

        $card = $image->urlFor('card');

        $this->assertStringContainsString('res.cloudinary.com/maison-test/image/upload/', $card);
        $this->assertStringContainsString('w_800,h_1067', $card);
        $this->assertStringContainsString('q_auto', $card);
        $this->assertStringContainsString('f_auto', $card);
        $this->assertStringContainsString('maison-modern/products/shirt', $card);
        $this->assertStringNotContainsString('cloudinary://', $card);
    }

    public function test_product_images_fall_back_to_local_storage_without_cloudinary(): void
    {
        config([
            'services.cloudinary.cloud_name' => null,
            'services.cloudinary.url' => null,
        ]);

        $image = ProductImage::factory()->create([
            'path' => 'products/local-shirt.jpg',
            'public_id' => null,
        ]);

        $this->assertSame(Storage::disk('public')->url('products/local-shirt.jpg'), $image->urlFor('card'));
    }

    public function test_http_image_paths_are_used_when_public_id_is_missing(): void
    {
        config(['services.cloudinary.cloud_name' => 'maison-test']);

        $image = ProductImage::factory()->create([
            'path' => 'https://cdn.example.com/shirt.jpg',
            'public_id' => null,
        ]);

        $this->assertSame('https://cdn.example.com/shirt.jpg', $image->urlFor('gallery'));
    }

    public function test_cloudinary_sync_is_skipped_without_credentials(): void
    {
        config([
            'services.cloudinary.url' => null,
            'services.cloudinary.cloud_name' => null,
            'services.cloudinary.api_key' => null,
            'services.cloudinary.api_secret' => null,
        ]);

        $service = Mockery::mock(CloudinaryImageService::class)->makePartial();
        $service->shouldReceive('upload')->never();
        $this->app->instance(CloudinaryImageService::class, $service);

        ProductImage::factory()->create([
            'path' => 'products/unsynced.jpg',
            'public_id' => null,
        ]);

        $this->assertTrue(true);
    }

    public function test_whatsapp_message_contains_order_details_without_sensitive_fields(): void
    {
        config(['shop.whatsapp_number' => '+212 6 12 34 56 78']);

        $product = Product::factory()->create(['name' => 'Atlas Shirt']);
        $order = Order::factory()->create([
            'customer_name' => 'Amina El Fassi',
            'phone' => '0612345678',
            'email' => 'amina@example.com',
            'address' => '12 Rue des Fleurs',
            'city' => 'Casablanca',
            'total' => '230.00',
        ]);
        OrderItem::factory()->for($order)->create([
            'product_id' => $product->id,
            'product_name' => 'Atlas Shirt',
            'selected_size' => 'M',
            'selected_color' => 'Black',
            'quantity' => 2,
        ]);

        $order->load('items');
        $message = WhatsAppOrder::message($order);
        $url = WhatsAppOrder::url($order);

        $this->assertTrue(WhatsAppOrder::isEnabled());
        $this->assertSame('212612345678', WhatsAppOrder::number());
        $this->assertStringContainsString('Maison Modern', $message);
        $this->assertStringContainsString($order->order_number, $message);
        $this->assertStringContainsString('Amina El Fassi', $message);
        $this->assertStringContainsString('Atlas Shirt (M / Black) x2', $message);
        $this->assertStringContainsString('230.00 MAD', $message);
        $this->assertStringContainsString('Cash on Delivery', $message);
        $this->assertStringNotContainsString('0612345678', $message);
        $this->assertStringNotContainsString('amina@example.com', $message);
        $this->assertStringNotContainsString('12 Rue des Fleurs', $message);
        $this->assertStringStartsWith('https://wa.me/212612345678?text=', $url);
    }

    public function test_order_confirmation_shows_whatsapp_when_configured(): void
    {
        config(['shop.whatsapp_number' => '212600000000']);

        $order = Order::factory()->create([
            'customer_name' => 'Amina El Fassi',
            'total' => '80.00',
        ]);
        OrderItem::factory()->for($order)->create([
            'product_name' => 'Riad Shirt',
            'quantity' => 1,
        ]);

        $this->get(route('order.confirmation', $order))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('Cash on Delivery')
            ->assertSee('Riad Shirt')
            ->assertSee('Confirm your order on WhatsApp')
            ->assertSee('https://wa.me/212600000000', false);
    }

    public function test_order_confirmation_hides_whatsapp_when_not_configured(): void
    {
        config(['shop.whatsapp_number' => '']);

        $order = Order::factory()->create();

        $this->get(route('order.confirmation', $order))
            ->assertOk()
            ->assertDontSee('Confirm your order on WhatsApp');
    }

    public function test_missing_storefront_pages_show_a_clean_not_found_state(): void
    {
        $this->get('/definitely-missing-page')
            ->assertNotFound()
            ->assertSee('Page not found');
    }

    public function test_customer_password_reset_and_account_routes_do_not_exist(): void
    {
        $this->get('/forgot-password')->assertNotFound();
        $this->get('/password/reset')->assertNotFound();
        $this->get('/account')->assertNotFound();
        $this->get('/dashboard')->assertNotFound();
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('password.request'));
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('password.reset'));
    }
}
