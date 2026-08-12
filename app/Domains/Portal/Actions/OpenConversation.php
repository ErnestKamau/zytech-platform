<?php

namespace App\Domains\Portal\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Portal\Services\MessageService;
use App\Models\PortalConversation;

final class OpenConversation extends BaseAction
{
    public function __construct(private readonly MessageService $messages) {}

    public function handle(mixed ...$arguments): PortalConversation
    {
        return $this->messages->open($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
    }
}
