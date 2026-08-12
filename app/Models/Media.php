<?php

namespace App\Models;

use App\Core\Enums\MediaType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

class Media extends SpatieMedia
{
    use HasUuids;

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(MediaTag::class, 'media_media_tag');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(MediaUsage::class);
    }

    public function mediaType(): MediaType
    {
        return MediaType::fromMime($this->mime_type);
    }

    public function alt(): string
    {
        return (string) $this->getCustomProperty('alt', $this->name);
    }

    public function isProtectedSiteAsset(): bool
    {
        return (bool) $this->getCustomProperty('protected_site_asset', false);
    }

    public function siteKey(): ?string
    {
        $key = $this->getCustomProperty('site_key');

        return is_string($key) && $key !== '' ? $key : null;
    }
}
