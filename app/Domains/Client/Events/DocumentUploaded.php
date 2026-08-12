<?php

namespace App\Domains\Client\Events;

use App\Core\Events\BusinessEvent;
use App\Models\ClientDocument;

final class DocumentUploaded extends BusinessEvent
{
    public function __construct(public ClientDocument $document) {}
}
