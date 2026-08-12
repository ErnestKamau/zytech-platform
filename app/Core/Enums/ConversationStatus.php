<?php

namespace App\Core\Enums;

enum ConversationStatus: string
{
    case Open = 'open';
    case Waiting = 'waiting';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Waiting => 'Waiting on client',
            self::Closed => 'Closed',
        };
    }
}
