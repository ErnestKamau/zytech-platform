<?php

namespace App\Domains\Media\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Media\Services\MediaService;
use App\Models\Media;

final class DeleteMedia extends BaseAction
{
    public function __construct(
        private readonly MediaService $media,
    ) {}

    public function handle(mixed ...$arguments): mixed
    {
        /** @var Media $media */
        $media = $arguments[0];

        $this->media->delete($media);

        return null;
    }
}
