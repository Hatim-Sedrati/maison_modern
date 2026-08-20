<?php

namespace App\Models;

use App\Enums\ProductGender;
use App\Models\Concerns\HasSlug;
use App\Support\Money;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'category_id',
    'name',
    'slug',
    'description',
    'short_description',
    'gender',
    'price',
    'compare_at_price',
    'sku',
    'stock',
    'is_active',
    'is_featured',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, HasSlug;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'gender' => ProductGender::class,
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)
            ->where('is_primary', true)
            ->orderBy('sort_order');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (Product $product): bool {
            return $product->orderItems()->doesntExist();
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeGender(Builder $query, ProductGender|string $gender): Builder
    {
        $value = $gender instanceof ProductGender ? $gender->value : $gender;

        return $query->where('gender', $value);
    }

    public function hasActiveVariants(): bool
    {
        if ($this->relationLoaded('variants')) {
            return $this->variants->contains(
                fn (ProductVariant $variant): bool => $variant->is_active,
            );
        }

        return $this->variants()->where('is_active', true)->exists();
    }

    public function availableStock(?ProductVariant $variant = null): int
    {
        if ($variant) {
            return $variant->is_active ? $variant->stock : 0;
        }

        if ($this->hasActiveVariants()) {
            return 0;
        }

        return $this->stock;
    }

    public function unitPrice(?ProductVariant $variant = null): string
    {
        if ($variant && $variant->price !== null) {
            return Money::of($variant->price);
        }

        return Money::of($this->price);
    }

    public function decrementAvailableStock(int $quantity, ?ProductVariant $variant = null): void
    {
        if ($quantity < 1) {
            return;
        }

        if ($variant) {
            $variant->decrement('stock', $quantity);

            return;
        }

        $this->decrement('stock', $quantity);
    }
}
