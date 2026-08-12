<?php

namespace App\Domains\Media\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Media\Events\MediaUploaded;
use App\Infrastructure\Queue\QueueName;
use Illuminate\Support\Facades\Log;

final class IndexMedia extends BaseListener
{
    public string $queue = QueueName::SEARCH;

    public function handle(MediaUploaded $event): void
    {
        Log::info('media.indexed', [
            'media_id' => $event->media->getKey(),
            'name' => $event->media->name,
        ]);
    }
}
