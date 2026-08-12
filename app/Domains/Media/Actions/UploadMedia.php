<?php

namespace App\Domains\Media\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Media\Data\MediaUploadData;
use App\Domains\Media\Services\MediaService;
use App\Models\Media;

final class UploadMedia extends BaseAction
{
    public function __construct(
        private readonly MediaService $media,
    ) {}

    public function handle(mixed ...$arguments): Media
    {
        /** @var MediaUploadData $data */
        $data = $arguments[0];

        return $this->media->upload($data);
    }
}
