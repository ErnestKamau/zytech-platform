<?php

namespace App\Domains\Client\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Client\Services\ClientService;
use App\Models\Client;

final class AssignPortalAccess extends BaseAction
{
    public function __construct(private readonly ClientService $clients) {}

    public function handle(mixed ...$arguments): Client
    {
        /** @var Client $client */
        $client = $arguments[0];
        /** @var string $userId */
        $userId = $arguments[1];

        return $this->clients->assignPortalAccess($client, $userId);
    }
}
