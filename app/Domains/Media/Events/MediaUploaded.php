<?php

namespace App\Domains\Media\Events;

use App\Core\Events\BusinessEvent;
use App\Models\Media;

final class MediaUploaded extends BusinessEvent
{
    public function __construct(public Media $media) {}
}
