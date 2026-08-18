<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSlug
{
    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);

        if ($base === '') {
            $base = 'item';
        }

        $slug = $base;
        $suffix = 1;

        while (static::query()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    protected static function bootHasSlug(): void
    {
        static::saving(function (Model $model): void {
            if (filled($model->slug)) {
                $model->slug = Str::slug((string) $model->slug);

                return;
            }

            $model->slug = static::generateUniqueSlug((string) $model->name, $model->getKey());
        });
    }
}
