<?php

namespace App\Domains\Media\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Media\Events\MediaUploaded;
use App\Domains\Media\Services\ImageOptimizationService;
use App\Infrastructure\Queue\QueueName;

final class GenerateResponsiveImages extends BaseListener
{
    public string $queue = QueueName::MEDIA;

    public function __construct(
        private readonly ImageOptimizationService $images,
    ) {}

    public function handle(MediaUploaded $event): void
    {
        $this->images->optimize($event->media);
    }
}
