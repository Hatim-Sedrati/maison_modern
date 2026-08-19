<?php

namespace App\Services;

use App\Models\ProductImage;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class CloudinaryImageService
{
    /**
     * @var array<string, string>
     */
    public const PRESETS = [
        'card' => 'c_fill,g_auto,w_800,h_1067,q_auto,f_auto',
        'thumb' => 'c_fill,g_auto,w_240,h_320,q_auto,f_auto',
        'gallery' => 'c_fit,w_1400,h_1867,q_auto:good,f_auto',
        'hero' => 'c_fill,g_auto,w_1600,h_2000,q_auto:good,f_auto',
    ];

    public function canDeliver(): bool
    {
        return filled($this->cloudName());
    }

    public function canUpload(): bool
    {
        return filled(config('services.cloudinary.url'))
            || (
                filled(config('services.cloudinary.cloud_name'))
                && filled(config('services.cloudinary.api_key'))
                && filled(config('services.cloudinary.api_secret'))
            );
    }

    public function cloudName(): ?string
    {
        $name = config('services.cloudinary.cloud_name');

        if (filled($name)) {
            return (string) $name;
        }

        $url = (string) config('services.cloudinary.url');

        if ($url !== '' && preg_match('/@([^\/?#]+)/', $url, $matches) === 1) {
            return $matches[1];
        }

        return null;
    }

    public function url(string $publicId, string $preset = 'gallery'): string
    {
        $cloud = $this->cloudName();
        $publicId = ltrim($publicId, '/');
        $transform = self::PRESETS[$preset] ?? self::PRESETS['gallery'];

        if (! $cloud) {
            return $publicId;
        }

        return 'https://res.cloudinary.com/'.$cloud.'/image/upload/'.$transform.'/'.$publicId;
    }

    public function deliveryUrl(ProductImage $image, string $preset = 'gallery'): string
    {
        if (filled($image->public_id) && $this->canDeliver()) {
            return $this->url((string) $image->public_id, $preset);
        }

        $path = (string) $image->path;

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if ($path === '') {
            return '';
        }

        return Storage::disk('public')->url($path);
    }

    public function syncUploadedImage(ProductImage $image): void
    {
        if (! $this->canUpload()) {
            return;
        }

        $path = (string) $image->path;

        if ($path === '' || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

        $absolute = Storage::disk('public')->path($path);

        if (! is_file($absolute)) {
            return;
        }

        try {
            if (filled($image->public_id)) {
                $this->destroy((string) $image->public_id);
            }

            $result = $this->upload($absolute);
        } catch (Throwable $e) {
            report($e);

            throw ValidationException::withMessages([
                'data.images' => 'The image could not be uploaded. Please try again.',
            ]);
        }

        $image->forceFill([
            'public_id' => $result['public_id'],
            'path' => $result['secure_url'],
        ])->saveQuietly();

        Storage::disk('public')->delete($path);
    }

    public function deleteRemote(ProductImage $image): void
    {
        if (filled($image->public_id) && $this->canUpload()) {
            try {
                $this->destroy((string) $image->public_id);
            } catch (Throwable $e) {
                report($e);
            }
        }

        $path = (string) $image->path;

        if ($path !== '' && ! str_starts_with($path, 'http://') && ! str_starts_with($path, 'https://')) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * @return array{public_id: string, secure_url: string}
     */
    public function upload(string $filePath, string $folder = 'maison-modern/products'): array
    {
        $result = $this->client()->uploadApi()->upload($filePath, [
            'folder' => $folder,
            'resource_type' => 'image',
            'unique_filename' => true,
            'use_filename' => true,
            'overwrite' => false,
            'allowed_formats' => ['jpg', 'jpeg', 'png', 'webp'],
        ]);

        return [
            'public_id' => (string) ($result['public_id'] ?? ''),
            'secure_url' => (string) ($result['secure_url'] ?? ''),
        ];
    }

    public function destroy(string $publicId): void
    {
        $this->client()->uploadApi()->destroy($publicId);
    }

    protected function client(): Cloudinary
    {
        $url = config('services.cloudinary.url');

        if (filled($url)) {
            return new Cloudinary((string) $url);
        }

        return new Cloudinary([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'api_key' => config('services.cloudinary.api_key'),
                'api_secret' => config('services.cloudinary.api_secret'),
            ],
            'url' => [
                'secure' => (bool) config('services.cloudinary.secure', true),
            ],
        ]);
    }
}
