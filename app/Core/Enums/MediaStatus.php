<?php

namespace App\Core\Enums;

enum MediaStatus: string
{
    case Ready = 'ready';
    case Processing = 'processing';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Ready => 'Ready',
            self::Processing => 'Processing',
            self::Failed => 'Failed',
        };
    }
}
