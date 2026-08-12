<?php

namespace App\Domains\Configuration\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Configuration\Services\ConfigurationService;

final class UpdateSettings extends BaseAction
{
    public function __construct(
        private readonly ConfigurationService $configuration,
    ) {}

    public function handle(mixed ...$arguments): mixed
    {
        /** @var array<string, mixed> $values */
        $values = $arguments[0];

        $this->configuration->updateMany($values);

        return null;
    }
}
