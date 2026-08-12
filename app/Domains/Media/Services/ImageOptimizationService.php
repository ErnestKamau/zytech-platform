<?php

namespace App\Domains\Media\Services;

use App\Core\Enums\MediaType;
use App\Core\Services\BaseService;
use App\Domains\Media\Events\MediaConverted;
use App\Domains\Media\Events\MediaOptimized;
use App\Models\Media;
use Spatie\MediaLibrary\Conversions\FileManipulator;

final class ImageOptimizationService extends BaseService
{
    public function __construct(
        private readonly FileManipulator $manipulator,
    ) {}

    public function optimize(Media $media): Media
    {
        if ($media->mediaType() !== MediaType::Image) {
            return $media;
        }

        $this->manipulator->createDerivedFiles($media);

        event(new MediaOptimized($media->fresh()));
        event(new MediaConverted($media->fresh()));

        return $media->refresh();
    }
}
