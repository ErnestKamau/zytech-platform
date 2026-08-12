<?php

namespace App\Domains\Communication\Events;

use App\Core\Events\BroadcastEvent;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;

final class NotificationPushed extends BroadcastEvent
{
    /**
     * @param  array<string, mixed>|null  $meta
     */
    public function __construct(
        public string $type,
        public string $title,
        public string $body,
        public ?string $userId = null,
        public ?array $meta = null,
    ) {}

    public function broadcastOn(): array
    {
        if ($this->userId !== null && $this->userId !== '') {
            return [new PrivateChannel('App.Models.User.'.$this->userId)];
        }

        return [new Channel('platform.announcements')];
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'body' => $this->body,
            'meta' => $this->meta,
        ];
    }
}
