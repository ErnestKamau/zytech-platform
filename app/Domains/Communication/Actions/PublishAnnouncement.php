<?php

namespace App\Domains\Communication\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Communication\Services\AnnouncementService;
use App\Models\Announcement;

final class PublishAnnouncement extends BaseAction
{
    public function __construct(private readonly AnnouncementService $announcements) {}

    public function handle(mixed ...$arguments): Announcement
    {
        /** @var array<string, mixed> $attributes */
        $attributes = $arguments[0];

        return $this->announcements->publish($attributes);
    }
}
