<?php

namespace App\Domains\Media\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Media\Events\MediaConverted;
use App\Domains\Media\Events\MediaDeleted;
use App\Domains\Media\Events\MediaMoved;
use App\Domains\Media\Events\MediaOptimized;
use App\Domains\Media\Events\MediaUploaded;
use App\Domains\Media\Services\MediaService;

final class ClearMediaCache extends BaseListener
{
    public function __construct(
        private readonly MediaService $media,
    ) {}

    public function handle(
        MediaUploaded|MediaDeleted|MediaMoved|MediaConverted|MediaOptimized $event,
    ): void {
        $this->media->forget();
    }
}
