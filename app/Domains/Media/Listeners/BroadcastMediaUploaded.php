<?php

namespace App\Domains\Media\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Media\Events\MediaDeleted;
use App\Domains\Media\Events\MediaMoved;
use App\Domains\Media\Events\MediaUploaded;
use App\Infrastructure\Queue\QueueName;
use Illuminate\Support\Facades\Log;

final class BroadcastMediaUploaded extends BaseListener
{
    public string $queue = QueueName::BROADCAST;

    public function handle(MediaUploaded|MediaDeleted|MediaMoved $event): void
    {
        Log::info('media.broadcast', [
            'event' => class_basename($event),
        ]);
    }
}
