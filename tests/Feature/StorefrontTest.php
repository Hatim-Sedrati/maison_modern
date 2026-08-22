<?php

namespace Tests\Feature;

use App\Livewire\Cart\CartPage;
use App\Livewire\Checkout\CheckoutForm;
use App\Livewire\Product\AddToCart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Services\Cart;
use App\Support\OrderConfirmation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_view_core_storefront_pages(): void
    {
        $this->assertGuest();

        $this->get(route('home'))->assertOk()->assertSee('Maison Modern');
        $this->get(route('shop.index'))->assertOk();
        $this->get(route('cart.index'))->assertOk()->assertSee('Your cart is empty');
        $this->get(route('checkout.index'))->assertOk();
        $this->get(route('pages.contact'))->assertOk();
        $this->get(route('pages.shipping'))->assertOk();
        $this->get(route('pages.returns'))->assertOk();
        $this->get(route('pages.faq'))->assertOk();
    }

    public function test_information_pages_are_available_and_linked_from_the_footer(): void
    {
        $this->get(route('pages.delivery'))
            ->assertOk()
            ->assertSee('Maison Modern delivers to all of Morocco')
            ->assertSee('3–7 days');

        $this->get('/delivery')->assertOk();
        $this->get('/cash-on-delivery')
            ->assertOk()
            ->assertSee('Cash on Delivery')
            ->assertSee('You pay when your order arrives');

        $this->get(route('pages.contact'))
            ->assertOk()
            ->assertSee('+212 6 32 65 26 92')
            ->assertSee('tel:+212632652692', false)
            ->assertSee('https://wa.me/212632652692', false)
            ->assertDontSee('To be completed')
            ->assertDontSee('SHOP_CONTACT_PHONE')
            ->assertDontSee('SHOP_CONTACT_EMAIL');

        $this->get(route('pages.returns'))
            ->assertOk()
            ->assertSee('within 1 day')
            ->assertSee('used or damaged by the customer')
            ->assertSee('exchange')
            ->assertSee('cash refund');

        $home = $this->get(route('home'))->assertOk();
        $home->assertSee(route('pages.delivery'), false);
        $home->assertSee(route('pages.cash-on-delivery'), false);
        $home->assertSee(route('pages.contact'), false);
        $home->assertSee(route('pages.returns'), false);
    }

    public function test_homepage_and_shop_use_database_products(): void
    {
        $product = Product::factory()->create(['name' => 'Atlas Linen Shirt', 'is_featured' => true]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Atlas Linen Shirt');

        $this->get(route('shop.index'))
            ->assertOk()
            ->assertSee('Atlas Linen Shirt');
    }

    public function test_shop_filters_by_gender_category_search_and_sort(): void
    {
        $clothing = Category::factory()->create(['name' => 'Clothing', 'slug' => 'clothing']);
        $accessories = Category::factory()->create(['name' => 'Accessories', 'slug' => 'accessories']);

        $women = Product::factory()->for($clothing)->create([
            'name' => 'Women Dress',
            'gender' => 'women',
            'price' => '400.00',
            'sku' => 'MM-DRESS-1',
        ]);
        $men = Product::factory()->for($clothing)->create([
            'name' => 'Men Jacket',
            'gender' => 'men',
            'price' => '200.00',
        ]);
        $bag = Product::factory()->for($accessories)->create([
            'name' => 'Canvas Tote',
            'gender' => 'unisex',
            'price' => '150.00',
        ]);

        $this->get(route('shop.index', ['gender' => 'women']))
            ->assertOk()
            ->assertSee($women->name)
            ->assertDontSee($men->name);

        $this->get(route('shop.index', ['category' => 'accessories']))
            ->assertOk()
            ->assertSee($bag->name)
            ->assertDontSee($women->name);

        $this->get(route('shop.index', ['search' => 'MM-DRESS-1']))
            ->assertOk()
            ->assertSee($women->name)
            ->assertDontSee($bag->name);

        $this->get(route('shop.index', ['sort' => 'price_asc']))
            ->assertOk()
            ->assertSeeInOrder(['Canvas Tote', 'Men Jacket', 'Women Dress']);
    }

    public function test_category_page_shows_only_that_category(): void
    {
        $shoes = Category::factory()->create(['name' => 'Shoes', 'slug' => 'shoes']);
        $other = Category::factory()->create(['name' => 'Clothing', 'slug' => 'clothing']);

        Product::factory()->for($shoes)->create(['name' => 'Leather Loafer']);
        Product::factory()->for($other)->create(['name' => 'Wool Coat']);

        $this->get(route('category.show', $shoes))
            ->assertOk()
            ->assertSee('Leather Loafer')
            ->assertSee('Shoes')
            ->assertDontSee('Wool Coat');
    }

    public function test_inactive_products_and_categories_are_not_public(): void
    {
        $product = Product::factory()->inactive()->create();
        $category = Category::factory()->create(['is_active' => false]);

        $this->get(route('product.show', $product))->assertNotFound();
        $this->get(route('category.show', $category))->assertNotFound();
    }

    public function test_product_page_shows_variants_and_gallery(): void
    {
        $product = Product::factory()->create(['name' => 'Casa Trousers']);
        ProductVariant::factory()->for($product)->create(['size' => 'M', 'color' => 'Black', 'stock' => 4]);
        ProductVariant::factory()->for($product)->create(['size' => 'L', 'color' => 'Black', 'stock' => 0]);
        ProductImage::factory()->for($product)->primary()->create(['path' => 'products/one.jpg']);
        ProductImage::factory()->for($product)->create(['path' => 'products/two.jpg', 'sort_order' => 1]);

        $this->get(route('product.show', $product))
            ->assertOk()
            ->assertSee('Casa Trousers')
            ->assertSee('Black')
            ->assertSee('M')
            ->assertSee('Add to cart');
    }

    public function test_guests_can_add_to_cart_and_see_cart_page(): void
    {
        $this->assertGuest();

        $product = Product::factory()->create(['name' => 'Riad Shirt', 'stock' => 5, 'price' => '250.00']);

        Livewire::test(AddToCart::class, ['product' => $product])
            ->set('quantity', 2)
            ->call('addToCart')
            ->assertSet('messageType', 'success')
            ->assertDispatched('cart-updated');

        $this->assertSame(2, app(Cart::class)->quantity());

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Riad Shirt')
            ->assertSee('500.00');
    }

    public function test_unavailable_variant_cannot_be_added_to_cart(): void
    {
        $product = Product::factory()->create(['stock' => 0]);
        $variant = ProductVariant::factory()->for($product)->create(['stock' => 0, 'size' => 'S', 'color' => 'Navy']);

        Livewire::test(AddToCart::class, ['product' => $product->fresh('variants')])
            ->set('variantId', $variant->id)
            ->call('addToCart')
            ->assertSet('messageType', 'error');

        $this->assertTrue(app(Cart::class)->isEmpty());
    }

    public function test_guests_can_place_a_cash_on_delivery_order(): void
    {
        $this->assertGuest();

        $product = Product::factory()->create(['stock' => 3, 'price' => '80.00']);
        app(Cart::class)->add($product->id, null, 1);

        Livewire::test(CheckoutForm::class)
            ->set('customer_name', 'Amina El Fassi')
            ->set('phone', '0612345678')
            ->set('city', 'Casablanca')
            ->set('address', '12 Rue des Fleurs')
            ->call('placeOrder')
            ->assertRedirect();

        $order = Order::query()->first();
        $this->assertNotNull($order);
        $this->assertSame('cash_on_delivery', $order->payment_method->value);

        $this->get(OrderConfirmation::url($order))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('Cash on Delivery');
    }

    public function test_cart_quantity_can_be_updated_and_removed(): void
    {
        $product = Product::factory()->create(['stock' => 5, 'price' => '100.00']);
        app(Cart::class)->add($product->id, null, 1);
        $key = app(Cart::class)->items()->first()['key'];

        Livewire::test(CartPage::class)
            ->call('updateQuantity', $key, 3)
            ->assertSee('300.00');

        $this->assertSame(3, app(Cart::class)->quantity());

        Livewire::test(CartPage::class)
            ->call('remove', $key)
            ->assertSee('Your cart is empty');
    }

    public function test_logo_is_used_in_the_storefront_header(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('images/logo.png', false);
    }

    public function test_pagination_is_applied_on_the_shop_page(): void
    {
        Product::factory()->count(13)->create();

        $this->get(route('shop.index'))
            ->assertOk()
            ->assertSee('Next');
    }
}
