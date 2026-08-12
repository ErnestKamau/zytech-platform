<?php

namespace App\Core\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case InProgress = 'in-progress';
    case Waiting = 'waiting';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::InProgress => 'In progress',
            self::Waiting => 'Waiting',
            self::Resolved => 'Resolved',
            self::Closed => 'Closed',
        };
    }
}
