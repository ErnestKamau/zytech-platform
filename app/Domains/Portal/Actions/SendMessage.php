<?php

namespace App\Domains\Portal\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Portal\Services\MessageService;
use App\Models\PortalMessage;

final class SendMessage extends BaseAction
{
    public function __construct(private readonly MessageService $messages) {}

    public function handle(mixed ...$arguments): PortalMessage
    {
        return $this->messages->send($arguments[0], $arguments[1], $arguments[2]);
    }
}
