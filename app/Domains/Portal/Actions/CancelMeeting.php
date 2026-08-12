<?php

namespace App\Domains\Portal\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Portal\Services\MeetingService;
use App\Models\MeetingRequest;

final class CancelMeeting extends BaseAction
{
    public function __construct(private readonly MeetingService $meetings) {}

    public function handle(mixed ...$arguments): MeetingRequest
    {
        return $this->meetings->cancel($arguments[0]);
    }
}
