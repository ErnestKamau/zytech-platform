<?php

namespace App\Domains\Media\Data;

use App\Core\Data\BaseDTO;
use App\Core\Enums\MediaCollection;

final readonly class MediaUploadData extends BaseDTO
{
    /**
     * @param  array<string, mixed>  $customProperties
     */
    public function __construct(
        public string $folderId,
        public MediaCollection $collection,
        public string $path,
        public string $name,
        public array $customProperties = [],
        public bool $preserveOriginal = false,
        public bool $protectedSiteAsset = false,
    ) {}

    public static function fromArray(array $data): static
    {
        $collection = $data['collection'] ?? MediaCollection::Gallery->value;

        return new self(
            folderId: (string) ($data['folder_id'] ?? ''),
            collection: $collection instanceof MediaCollection
                ? $collection
                : MediaCollection::from((string) $collection),
            path: (string) ($data['path'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            customProperties: is_array($data['custom_properties'] ?? null) ? $data['custom_properties'] : [],
            preserveOriginal: (bool) ($data['preserve_original'] ?? false),
            protectedSiteAsset: (bool) ($data['protected_site_asset'] ?? false),
        );
    }

    public function toArray(): array
    {
        return [
            'folder_id' => $this->folderId,
            'collection' => $this->collection->value,
            'path' => $this->path,
            'name' => $this->name,
            'custom_properties' => $this->customProperties,
            'preserve_original' => $this->preserveOriginal,
            'protected_site_asset' => $this->protectedSiteAsset,
        ];
    }
}
