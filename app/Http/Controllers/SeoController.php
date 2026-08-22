<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function robots(): Response
    {
        $sitemap = url('/sitemap.xml');

        $body = <<<TXT
User-agent: *
Allow: /
Disallow: /admin
Disallow: /admin/
Disallow: /livewire
Disallow: /cart
Disallow: /checkout
Disallow: /order/

Sitemap: {$sitemap}

TXT;

        return response($body, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    public function sitemap(): Response
    {
        $urls = $this->publicUrls();

        return response()
            ->view('seo.sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * @return list<array{loc: string, lastmod: string|null}>
     */
    protected function publicUrls(): array
    {
        $urls = [
            ['loc' => route('home'), 'lastmod' => null],
            ['loc' => route('shop.index'), 'lastmod' => null],
            ['loc' => route('pages.delivery'), 'lastmod' => null],
            ['loc' => route('pages.cash-on-delivery'), 'lastmod' => null],
            ['loc' => route('pages.contact'), 'lastmod' => null],
            ['loc' => route('pages.returns'), 'lastmod' => null],
            ['loc' => route('pages.faq'), 'lastmod' => null],
        ];

        Category::query()
            ->active()
            ->orderBy('name')
            ->each(function (Category $category) use (&$urls): void {
                $urls[] = [
                    'loc' => route('category.show', $category),
                    'lastmod' => $category->updated_at?->toAtomString(),
                ];
            });

        Product::query()
            ->active()
            ->with('category')
            ->orderBy('name')
            ->each(function (Product $product) use (&$urls): void {
                $urls[] = [
                    'loc' => route('product.show', $product),
                    'lastmod' => $product->updated_at?->toAtomString(),
                ];
            });

        return $urls;
    }
}
