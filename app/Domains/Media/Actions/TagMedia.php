<?php

namespace App\Domains\Media\Actions;

use App\Core\Actions\BaseAction;
use App\Models\Media;
use App\Models\MediaTag;
use Illuminate\Support\Collection;

final class TagMedia extends BaseAction
{
    public function handle(mixed ...$arguments): Media
    {
        /** @var Media $media */
        $media = $arguments[0];
        /** @var Collection<int, MediaTag>|list<string> $tags */
        $tags = $arguments[1];

        $ids = $tags instanceof Collection
            ? $tags->map(fn (MediaTag|string $tag): string => $tag instanceof MediaTag ? (string) $tag->getKey() : $tag)
            : collect($tags);

        $media->tags()->sync($ids->all());

        return $media->refresh();
    }
}
