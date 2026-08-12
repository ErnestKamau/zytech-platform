<?php

namespace App\Domains\Portal\Actions;

use App\Core\Actions\BaseAction;
use App\Core\Enums\MeetingType;
use App\Domains\Portal\Services\MeetingService;
use App\Models\MeetingRequest;

final class ScheduleMeeting extends BaseAction
{
    public function __construct(private readonly MeetingService $meetings) {}

    public function handle(mixed ...$arguments): MeetingRequest
    {
        $type = $arguments[1];
        if (! $type instanceof MeetingType) {
            $type = MeetingType::from((string) $type);
        }

        return $this->meetings->schedule(
            $arguments[0],
            $type,
            $arguments[2] ?? null,
            $arguments[3] ?? null,
            $arguments[4] ?? null,
            $arguments[5] ?? null,
        );
    }
}
