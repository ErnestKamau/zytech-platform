<?php

namespace App\Domains\Media\Events;

use App\Core\Events\BusinessEvent;

final class MediaDeleted extends BusinessEvent
{
    public function __construct(public string $mediaId) {}
}
