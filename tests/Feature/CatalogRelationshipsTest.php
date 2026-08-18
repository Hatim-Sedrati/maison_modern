<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_has_many_products(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->for($category)->create();

        $this->assertTrue($category->products->contains($product));
        $this->assertTrue($product->category->is($category));
    }

    public function test_product_has_many_variants(): void
    {
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->for($product)->create([
            'size' => 'M',
            'color' => 'Black',
        ]);

        $this->assertTrue($product->variants->contains($variant));
        $this->assertTrue($variant->product->is($product));
    }

    public function test_product_has_many_images(): void
    {
        $product = Product::factory()->create();
        $image = ProductImage::factory()->for($product)->primary()->create();

        $this->assertTrue($product->images->contains($image));
        $this->assertTrue($image->product->is($product));
        $this->assertTrue($product->primaryImage->is($image));
    }
}
