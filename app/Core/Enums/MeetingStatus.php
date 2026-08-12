<?php

namespace App\Core\Enums;

enum MeetingStatus: string
{
    case Requested = 'requested';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Requested => 'Requested',
            self::Confirmed => 'Confirmed',
            self::Cancelled => 'Cancelled',
            self::Completed => 'Completed',
        };
    }
}
