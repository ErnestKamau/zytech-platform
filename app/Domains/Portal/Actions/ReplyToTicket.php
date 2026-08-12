<?php

namespace App\Domains\Portal\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Portal\Services\SupportService;
use App\Models\SupportReply;

final class ReplyToTicket extends BaseAction
{
    public function __construct(private readonly SupportService $support) {}

    public function handle(mixed ...$arguments): SupportReply
    {
        return $this->support->reply(
            $arguments[0],
            $arguments[1],
            $arguments[2],
            (bool) ($arguments[3] ?? false),
        );
    }
}
