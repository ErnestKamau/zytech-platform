<?php

namespace App\Domains\Media\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Media\Services\ImageOptimizationService;
use App\Models\Media;

final class GenerateConversions extends BaseAction
{
    public function __construct(
        private readonly ImageOptimizationService $images,
    ) {}

    public function handle(mixed ...$arguments): Media
    {
        /** @var Media $media */
        $media = $arguments[0];

        return $this->images->optimize($media);
    }
}
