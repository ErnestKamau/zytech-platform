<?php

namespace App\Domains\Media\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Enums\MediaCollection;
use App\Core\Enums\MediaType;
use App\Core\Services\BaseService;
use App\Domains\Media\Data\MediaMetadata;
use App\Domains\Media\Data\MediaUploadData;
use App\Domains\Media\Events\MediaDeleted;
use App\Domains\Media\Events\MediaMoved;
use App\Domains\Media\Events\MediaUploaded;
use App\Domains\Media\Repositories\MediaFolderRepository;
use App\Domains\Media\Repositories\MediaRepository;
use App\Domains\Media\Support\MediaCache;
use App\Models\Media;
use App\Models\MediaFolder;
use Illuminate\Support\Collection;
use RuntimeException;
use Spatie\MediaLibrary\MediaCollections\FileAdder;

final class MediaService extends BaseService
{
    public function __construct(
        private readonly MediaRepository $media,
        private readonly MediaFolderRepository $folders,
        private readonly CacheStore $cache,
    ) {}

    public function upload(MediaUploadData $data): Media
    {
        $folder = MediaFolder::query()->findOrFail($data->folderId);

        if (! is_file($data->path)) {
            throw new RuntimeException("Media file not found: {$data->path}");
        }

        /** @var FileAdder $adder */
        $adder = $folder->addMedia($data->path)
            ->usingName($data->name !== '' ? $data->name : pathinfo($data->path, PATHINFO_FILENAME))
            ->withCustomProperties([
                ...$data->customProperties,
                'protected_site_asset' => $data->protectedSiteAsset,
            ]);

        if ($data->preserveOriginal) {
            $adder->preservingOriginal();
        }

        $media = $adder->toMediaCollection($data->collection->value);

        $this->forget();
        event(new MediaUploaded($media));

        return $media;
    }

    public function delete(Media $media): void
    {
        if ($media->isProtectedSiteAsset()) {
            throw new RuntimeException('Website media files cannot be deleted from the library.');
        }

        $id = (string) $media->getKey();
        $media->delete();

        $this->forget();
        event(new MediaDeleted($id));
    }

    public function move(Media $media, MediaFolder $folder, ?MediaCollection $collection = null): Media
    {
        $targetCollection = $collection?->value ?? $media->collection_name;
        $moved = $media->move($folder, $targetCollection);

        $this->forget();
        event(new MediaMoved($moved, $folder));

        return $moved;
    }

    /**
     * @return Collection<int, MediaFolder>
     */
    public function folderTree(): Collection
    {
        $cached = $this->cache->get(MediaCache::FOLDER_TREE);

        if ($cached instanceof Collection) {
            return $cached;
        }

        $this->cache->forget(MediaCache::FOLDER_TREE);
        $tree = $this->folders->roots();
        $this->cache->put(MediaCache::FOLDER_TREE, $tree, now()->addHour());

        return $tree;
    }

    /**
     * @return Collection<int, Media>
     */
    public function recent(int $limit = 24): Collection
    {
        return MediaCache::rememberCollection(
            $this->cache,
            MediaCache::RECENT,
            fn (): Collection => $this->media->recent($limit),
        );
    }

    public function metadata(Media $media): MediaMetadata
    {
        $thumb = null;

        if ($media->mediaType() === MediaType::Image && $media->hasGeneratedConversion('thumb')) {
            $thumb = $media->getUrl('thumb');
        }

        return MediaMetadata::fromArray([
            'id' => (string) $media->getKey(),
            'name' => $media->name,
            'file_name' => $media->file_name,
            'mime_type' => (string) $media->mime_type,
            'type' => $media->mediaType()->value,
            'size' => (int) $media->size,
            'url' => $media->getUrl(),
            'alt' => $media->alt(),
            'thumb_url' => $thumb,
        ]);
    }

    public function forget(): void
    {
        foreach (MediaCache::all() as $key) {
            $this->cache->forget($key);
        }
    }
}
