<?php

namespace App\Domains\Media\Actions;

use App\Core\Actions\BaseAction;
use App\Core\Enums\MediaCollection;
use App\Domains\Media\Services\MediaService;
use App\Models\Media;
use App\Models\MediaFolder;

final class MoveMedia extends BaseAction
{
    public function __construct(
        private readonly MediaService $media,
    ) {}

    public function handle(mixed ...$arguments): Media
    {
        /** @var Media $item */
        $item = $arguments[0];
        /** @var MediaFolder $folder */
        $folder = $arguments[1];
        /** @var MediaCollection|null $collection */
        $collection = $arguments[2] ?? null;

        return $this->media->move($item, $folder, $collection);
    }
}
