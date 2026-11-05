<?php

namespace App\Domains\Media\Support;

use App\Domains\Media\Repositories\MediaRepository;
use App\Models\Media;

/**
 * Resolves `image_key`/`video_key` values against config/zyntech-media.php first,
 * falling back to media uploaded via the Filament Media Library and tagged with a
 * matching `site_key` custom property. Lets catalog entries be added through the
 * admin UI instead of requiring a file deploy + config edit.
 */
final class MediaCatalog
{
    public static function image(?string $key): ?array
    {
        return self::resolve('images', 'image/', $key);
    }

    public static function video(?string $key): ?array
    {
        return self::resolve('videos', 'video/', $key);
    }

    /**
     * @return array<string, string>
     */
    public static function imageOptions(): array
    {
        return self::options('images', 'image/');
    }

    /**
     * @return array<string, string>
     */
    public static function videoOptions(): array
    {
        return self::options('videos', 'video/');
    }

    private static function resolve(string $configGroup, string $mimePrefix, ?string $key): ?array
    {
        if (! $key) {
            return null;
        }

        $configEntry = config("zyntech-media.{$configGroup}.{$key}");
        if (is_array($configEntry)) {
            return $configEntry;
        }

        $media = app(MediaRepository::class)->findBySiteKey($key);
        if (! $media instanceof Media || ! str_starts_with($media->mime_type ?? '', $mimePrefix)) {
            return null;
        }

        return [
            'path' => $media->getUrl(),
            'alt' => $media->alt(),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function options(string $configGroup, string $mimePrefix): array
    {
        $configOptions = collect(config("zyntech-media.{$configGroup}", []))
            ->mapWithKeys(fn (array $entry, string $key): array => [$key => str_replace('_', ' ', $key)]);

        $uploadedOptions = app(MediaRepository::class)->taggedWithSiteKey($mimePrefix)
            ->mapWithKeys(fn (Media $media): array => [
                $media->siteKey() => ($media->siteKey() ?? '').' (uploaded)',
            ]);

        return $configOptions->merge($uploadedOptions)->all();
    }
}
