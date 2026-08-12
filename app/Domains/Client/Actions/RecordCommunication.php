<?php

namespace App\Domains\Client\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Client\Services\CommunicationService;
use App\Models\Client;
use App\Models\ClientCommunication;

final class RecordCommunication extends BaseAction
{
    public function __construct(private readonly CommunicationService $communications) {}

    public function handle(mixed ...$arguments): ClientCommunication
    {
        /** @var Client $client */
        $client = $arguments[0];
        /** @var array<string, mixed> $data */
        $data = $arguments[1];

        return $this->communications->log($client, $data);
    }
}
