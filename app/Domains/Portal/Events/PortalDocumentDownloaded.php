<?php

namespace App\Domains\Portal\Events;

use App\Core\Events\BusinessEvent;
use App\Models\PortalDownload;

final class PortalDocumentDownloaded extends BusinessEvent
{
    public function __construct(public PortalDownload $download) {}
}
