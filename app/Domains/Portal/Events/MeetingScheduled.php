<?php

namespace App\Domains\Portal\Events;

use App\Core\Events\BusinessEvent;
use App\Models\MeetingRequest;

final class MeetingScheduled extends BusinessEvent
{
    public function __construct(public MeetingRequest $meeting) {}
}
