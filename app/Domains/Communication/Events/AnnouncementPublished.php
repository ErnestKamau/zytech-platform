<?php

namespace App\Domains\Communication\Events;

use App\Core\Events\BusinessEvent;
use App\Models\Announcement;

final class AnnouncementPublished extends BusinessEvent
{
    public function __construct(public Announcement $announcement) {}
}
