<?php

namespace App\Support;

use App\Models\Product;
use App\Models\ProductVariant;

final class StructuredData
{
    /**
     * @param  array<int, array<string, mixed>>  $additional
     * @return array<string, mixed>
     */
    public static function graph(array $additional = []): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => array_values(array_filter([
                self::organization(),
                self::website(),
                ...$additional,
            ])),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function organization(): array
    {
        return [
            '@type' => 'Organization',
            '@id' => rtrim(url('/'), '/').'/#organization',
            'name' => 'Maison Modern',
            'url' => url('/'),
            'logo' => asset('images/logo.png'),
            'telephone' => '+212632652692',
            'areaServed' => [
                '@type' => 'Country',
                'name' => 'Morocco',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function website(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => rtrim(url('/'), '/').'/#website',
            'name' => 'Maison Modern',
            'url' => url('/'),
            'publisher' => [
                '@id' => rtrim(url('/'), '/').'/#organization',
            ],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => route('shop.index').'?search={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function product(Product $product): ?array
    {
        $description = trim(strip_tags((string) ($product->short_description ?: $product->description ?: $product->name)));
        $images = $product->images
            ->map(fn ($image): string => $image->urlFor('gallery'))
            ->filter()
            ->values()
            ->all();

        $data = [
            '@type' => 'Product',
            'name' => $product->name,
            'url' => route('product.show', $product),
            'sku' => $product->sku,
            'brand' => [
                '@type' => 'Brand',
                'name' => 'Maison Modern',
            ],
            'offers' => self::offers($product),
        ];

        if ($description !== '') {
            $data['description'] = $description;
        }

        if ($images !== []) {
            $data['image'] = $images;
        }

        if ($product->category) {
            $data['category'] = $product->category->name;
        }

        return $data;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function productDocument(Product $product): ?array
    {
        $productData = self::product($product);

        if ($productData === null) {
            return null;
        }

        return ['@context' => 'https://schema.org'] + $productData;
    }

    /**
     * @return array<string, mixed>
     */
    protected static function offers(Product $product): array
    {
        $inStock = self::isInStock($product);
        $availability = $inStock
            ? 'https://schema.org/InStock'
            : 'https://schema.org/OutOfStock';

        $prices = self::offerPrices($product);
        $low = $prices[0];
        $high = $prices[count($prices) - 1];

        $shared = [
            'priceCurrency' => 'MAD',
            'availability' => $availability,
            'itemCondition' => 'https://schema.org/NewCondition',
            'url' => route('product.show', $product),
            'seller' => [
                '@id' => rtrim(url('/'), '/').'/#organization',
            ],
        ];

        if ($low !== $high) {
            return [
                '@type' => 'AggregateOffer',
                'lowPrice' => $low,
                'highPrice' => $high,
                'offerCount' => (string) count($prices),
                ...$shared,
            ];
        }

        return [
            '@type' => 'Offer',
            'price' => $low,
            ...$shared,
        ];
    }

    /**
     * @return list<string>
     */
    protected static function offerPrices(Product $product): array
    {
        if ($product->hasActiveVariants()) {
            $prices = $product->variants
                ->filter(fn (ProductVariant $variant): bool => $variant->is_active)
                ->map(fn (ProductVariant $variant): string => $product->unitPrice($variant))
                ->unique()
                ->sort()
                ->values()
                ->all();

            if ($prices !== []) {
                return $prices;
            }
        }

        return [Money::of($product->price)];
    }

    protected static function isInStock(Product $product): bool
    {
        if ($product->hasActiveVariants()) {
            return $product->variants->contains(
                fn (ProductVariant $variant): bool => $variant->is_active && $variant->stock > 0,
            );
        }

        return $product->stock > 0;
    }
}
