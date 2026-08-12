<?php

namespace App\Domains\Client\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Client\Services\ClientService;
use App\Models\Client;

final class ArchiveClient extends BaseAction
{
    public function __construct(private readonly ClientService $clients) {}

    public function handle(mixed ...$arguments): Client
    {
        /** @var Client $client */
        $client = $arguments[0];
        $notes = isset($arguments[1]) ? (string) $arguments[1] : null;

        return $this->clients->archive($client, $notes);
    }
}
