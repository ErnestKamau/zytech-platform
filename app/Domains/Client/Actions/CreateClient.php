<?php

namespace App\Domains\Client\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Client\Services\ClientService;
use App\Models\Client;

final class CreateClient extends BaseAction
{
    public function __construct(private readonly ClientService $clients) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(mixed ...$arguments): Client
    {
        /** @var array<string, mixed> $attributes */
        $attributes = $arguments[0];

        return $this->clients->create($attributes);
    }
}
