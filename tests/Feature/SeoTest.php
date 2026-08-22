<?php

namespace Tests\Feature;

use App\Livewire\Cart\CartPage;
use App\Models\Category;
use App\Models\Product;
use App\Services\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_txt_allows_the_storefront_and_blocks_admin(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('User-agent: *', false)
            ->assertSee('Allow: /', false)
            ->assertSee('Disallow: /admin', false)
            ->assertSee('Disallow: /cart', false)
            ->assertSee('Disallow: /checkout', false)
            ->assertSee('Disallow: /livewire', false)
            ->assertSee('Disallow: /order/', false)
            ->assertSee('Sitemap: '.url('/sitemap.xml'), false);
    }

    public function test_sitemap_includes_public_pages_and_uses_the_configured_app_url(): void
    {
        config(['app.url' => 'https://maison-modern.test']);
        URL::forceRootUrl('https://maison-modern.test');
        URL::forceScheme('https');

        $category = Category::factory()->create(['name' => 'Dresses', 'slug' => 'dresses', 'is_active' => true]);
        $product = Product::factory()->for($category)->create([
            'name' => 'Atlas Linen Dress',
            'slug' => 'atlas-linen-dress',
            'is_active' => true,
        ]);
        $hidden = Product::factory()->inactive()->create(['slug' => 'hidden-piece']);

        $response = $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $response->assertSee('https://maison-modern.test', false);
        $response->assertSee(route('home'), false);
        $response->assertSee(route('shop.index'), false);
        $response->assertSee(route('pages.delivery'), false);
        $response->assertSee(route('pages.cash-on-delivery'), false);
        $response->assertSee(route('pages.contact'), false);
        $response->assertSee(route('pages.returns'), false);
        $response->assertSee(route('pages.faq'), false);
        $response->assertSee(route('category.show', $category), false);
        $response->assertSee(route('product.show', $product), false);
        $response->assertDontSee(route('product.show', $hidden), false);
        $response->assertDontSee('/admin', false);
        $response->assertDontSee('/cart', false);
        $response->assertDontSee('/checkout', false);
        $response->assertDontSee('/shipping', false);
        $response->assertDontSee(route('pages.shipping'), false);
    }

    public function test_homepage_has_search_metadata_and_organization_schema(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<title>Maison Modern — Moroccan Fashion</title>', false)
            ->assertSee('<meta name="description"', false)
            ->assertSee('rel="canonical"', false)
            ->assertSee(route('home'), false)
            ->assertSee('application/ld+json', false)
            ->assertSee('"@type":"Organization"', false)
            ->assertSee('"@type":"WebSite"', false)
            ->assertSee('"name":"Maison Modern"', false)
            ->assertDontSee('aggregateRating', false)
            ->assertDontSee('CLOUDINARY_API_SECRET', false)
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_product_page_uses_real_product_data_for_seo(): void
    {
        $product = Product::factory()->create([
            'name' => 'Atlas Linen Shirt',
            'short_description' => 'A considered linen shirt.',
            'price' => '450.00',
            'stock' => 3,
            'sku' => 'MM-SHIRT-01',
        ]);

        $this->get(route('product.show', $product))
            ->assertOk()
            ->assertSee('<title>Atlas Linen Shirt — Maison Modern</title>', false)
            ->assertSee('A considered linen shirt.', false)
            ->assertSee('"@type":"Product"', false)
            ->assertSee('"sku":"MM-SHIRT-01"', false)
            ->assertSee('"price":"450.00"', false)
            ->assertSee('"priceCurrency":"MAD"', false)
            ->assertSee('InStock', false)
            ->assertDontSee('aggregateRating', false)
            ->assertDontSee('reviewCount', false);
    }

    public function test_cart_and_checkout_are_not_indexed(): void
    {
        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('noindex', false);

        $this->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('noindex', false);
    }

    public function test_search_results_are_not_indexed(): void
    {
        $this->get(route('shop.index', ['search' => 'linen']))
            ->assertOk()
            ->assertSee('noindex', false);
    }

    public function test_shipping_canonical_points_to_delivery(): void
    {
        $this->get(route('pages.shipping'))
            ->assertOk()
            ->assertSee('rel="canonical"', false)
            ->assertSee(route('pages.delivery'), false);
    }

    public function test_admin_login_is_marked_noindex(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('noindex', false);
    }

    public function test_cart_can_be_emptied_from_the_cart_page(): void
    {
        $product = Product::factory()->create(['name' => 'Clear Me Shirt', 'stock' => 4]);
        app(Cart::class)->add($product->id, null, 1);

        Livewire::test(CartPage::class)
            ->assertSee('Clear Me Shirt')
            ->call('clearCart')
            ->assertSee('Your cart is empty');
    }
}
