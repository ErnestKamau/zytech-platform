<?php

namespace Database\Seeders;

use App\Core\Enums\MediaCollection;
use App\Core\Enums\MediaVisibility;
use App\Domains\Media\Actions\UploadMedia;
use App\Domains\Media\Data\MediaUploadData;
use App\Domains\Media\Repositories\MediaRepository;
use App\Domains\Media\Services\MediaUsageService;
use App\Models\Company;
use App\Models\MediaFolder;
use App\Models\MediaTag;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $website = $this->folder('Website');
        $images = $this->folder('Images', $website);
        $videos = $this->folder('Videos', $website);
        $posters = $this->folder('Posters', $website);

        $tag = MediaTag::query()->updateOrCreate(
            ['slug' => 'website'],
            ['name' => 'Website'],
        );

        $company = Company::query()->first();

        foreach (config('zyntech-media.images', []) as $key => $asset) {
            $this->import(
                folder: $images,
                collection: MediaCollection::Gallery,
                relativePath: $asset['path'],
                siteKey: "image.{$key}",
                alt: $asset['alt'] ?? '',
                tag: $tag,
                company: $company,
                context: "website.image.{$key}",
            );
        }

        foreach (config('zyntech-media.videos', []) as $key => $asset) {
            $this->import(
                folder: $videos,
                collection: MediaCollection::Videos,
                relativePath: $asset['path'],
                siteKey: "video.{$key}",
                alt: $asset['alt'] ?? '',
                tag: $tag,
                company: $company,
                context: "website.video.{$key}",
                extra: [
                    'poster_path' => $asset['poster'] ?? null,
                ],
            );

            if (! empty($asset['poster'])) {
                $this->import(
                    folder: $posters,
                    collection: MediaCollection::Homepage,
                    relativePath: $asset['poster'],
                    siteKey: "poster.{$key}",
                    alt: $asset['alt'] ?? '',
                    tag: $tag,
                    company: $company,
                    context: "website.poster.{$key}",
                );
            }
        }
    }

    private function folder(string $name, ?MediaFolder $parent = null): MediaFolder
    {
        return MediaFolder::query()->updateOrCreate(
            [
                'name' => $name,
                'parent_id' => $parent?->id,
            ],
            [
                'visibility' => MediaVisibility::Public,
                'sort_order' => 0,
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function import(
        MediaFolder $folder,
        MediaCollection $collection,
        string $relativePath,
        string $siteKey,
        string $alt,
        MediaTag $tag,
        ?Company $company,
        string $context,
        array $extra = [],
    ): void {
        if (app(MediaRepository::class)->findBySiteKey($siteKey) !== null) {
            return;
        }

        $absolute = public_path($relativePath);

        if (! is_file($absolute)) {
            return;
        }

        $media = app(UploadMedia::class)->handle(MediaUploadData::fromArray([
            'folder_id' => $folder->id,
            'collection' => $collection->value,
            'path' => $absolute,
            'name' => pathinfo($relativePath, PATHINFO_FILENAME),
            'preserve_original' => true,
            'protected_site_asset' => true,
            'custom_properties' => [
                'alt' => $alt,
                'site_key' => $siteKey,
                'public_path' => $relativePath,
                ...array_filter($extra),
            ],
        ]));

        $media->tags()->syncWithoutDetaching([$tag->id]);

        if ($company !== null) {
            app(MediaUsageService::class)->attach($media, $company, $context);
        }
    }
}
