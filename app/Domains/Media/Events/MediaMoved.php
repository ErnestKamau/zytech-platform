<?php

namespace App\Domains\Media\Events;

use App\Core\Events\BusinessEvent;
use App\Models\Media;
use App\Models\MediaFolder;

final class MediaMoved extends BusinessEvent
{
    public function __construct(
        public Media $media,
        public MediaFolder $folder,
    ) {}
}
