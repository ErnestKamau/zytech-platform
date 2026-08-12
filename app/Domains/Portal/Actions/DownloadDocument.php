<?php

namespace App\Domains\Portal\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Portal\Services\PortalService;
use App\Models\PortalDownload;

final class DownloadDocument extends BaseAction
{
    public function __construct(private readonly PortalService $portal) {}

    public function handle(mixed ...$arguments): PortalDownload
    {
        return $this->portal->recordDownload($arguments[0], $arguments[1], $arguments[2]);
    }
}
