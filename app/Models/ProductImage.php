<?php

namespace App\Models;

use App\Services\CloudinaryImageService;
use Database\Factories\ProductImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'product_id',
    'path',
    'public_id',
    'sort_order',
    'is_primary',
])]
class ProductImage extends Model
{
    /** @use HasFactory<ProductImageFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_primary' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (ProductImage $image): void {
            if (! $image->is_primary || ! $image->product_id) {
                return;
            }

            static::query()
                ->where('product_id', $image->product_id)
                ->when($image->exists, fn ($query) => $query->whereKeyNot($image->getKey()))
                ->update(['is_primary' => false]);
        });

        static::saved(function (ProductImage $image): void {
            app(CloudinaryImageService::class)->syncUploadedImage($image);
        });

        static::deleted(function (ProductImage $image): void {
            app(CloudinaryImageService::class)->deleteRemote($image);
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function urlFor(string $preset = 'gallery'): string
    {
        return app(CloudinaryImageService::class)->deliveryUrl($this, $preset);
    }

    public function getUrlAttribute(): string
    {
        return $this->urlFor('gallery');
    }
}
