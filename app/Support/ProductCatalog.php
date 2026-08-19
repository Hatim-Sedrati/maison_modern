<?php

namespace App\Support;

use App\Enums\ProductGender;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductCatalog
{
    /**
     * @return array{
     *     search: string|null,
     *     gender: string|null,
     *     category: string|null,
     *     sort: string,
     *     in_stock: bool
     * }
     */
    public static function filtersFromRequest(Request $request): array
    {
        return [
            'search' => $request->string('search')->trim()->toString() ?: null,
            'gender' => $request->string('gender')->trim()->toString() ?: null,
            'category' => $request->string('category')->trim()->toString() ?: null,
            'sort' => $request->string('sort')->trim()->toString() ?: 'featured',
            'in_stock' => $request->boolean('in_stock'),
        ];
    }

    public static function query(array $filters = [], ?Category $category = null): Builder
    {
        $query = Product::query()
            ->active()
            ->with(['category', 'images', 'variants' => fn ($q) => $q->active()]);

        if ($category) {
            $query->where('category_id', $category->id);
        } elseif (! empty($filters['category'])) {
            $query->whereHas('category', fn (Builder $q) => $q
                ->where('slug', $filters['category'])
                ->where('is_active', true));
        }

        if (! empty($filters['gender']) && in_array($filters['gender'], ['men', 'women', 'unisex'], true)) {
            $query->gender(ProductGender::from($filters['gender']));
        }

        if (! empty($filters['search'])) {
            $term = '%'.$filters['search'].'%';
            $query->where(function (Builder $q) use ($term): void {
                $q->where('name', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhere('short_description', 'like', $term)
                    ->orWhere('sku', 'like', $term);
            });
        }

        if (! empty($filters['in_stock'])) {
            $query->where(function (Builder $q): void {
                $q->where(function (Builder $simple): void {
                    $simple->whereDoesntHave('variants', fn (Builder $v) => $v->where('is_active', true))
                        ->where('stock', '>', 0);
                })->orWhereHas('variants', fn (Builder $v) => $v
                    ->where('is_active', true)
                    ->where('stock', '>', 0));
            });
        }

        return match ($filters['sort'] ?? 'featured') {
            'newest' => $query->latest(),
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default => $query->orderByDesc('is_featured')->latest(),
        };
    }

    public static function paginate(Request $request, ?Category $category = null, int $perPage = 12): LengthAwarePaginator
    {
        $filters = self::filtersFromRequest($request);

        return self::query($filters, $category)
            ->paginate($perPage)
            ->withQueryString();
    }
}
