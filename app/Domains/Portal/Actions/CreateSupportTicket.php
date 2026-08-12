<?php

namespace App\Domains\Portal\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Portal\Services\SupportService;
use App\Models\SupportTicket;

final class CreateSupportTicket extends BaseAction
{
    public function __construct(private readonly SupportService $support) {}

    public function handle(mixed ...$arguments): SupportTicket
    {
        return $this->support->open($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
    }
}
