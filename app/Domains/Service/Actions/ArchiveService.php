<?php

namespace App\Domains\Service\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Service\Services\ServiceService;
use App\Models\Service;

final class ArchiveService extends BaseAction
{
    public function __construct(
        private readonly ServiceService $services,
    ) {}

    public function handle(mixed ...$arguments): Service
    {
        /** @var Service $service */
        $service = $arguments[0];

        return $this->services->archive($service);
    }
}
