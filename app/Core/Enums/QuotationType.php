<?php

namespace App\Core\Enums;

enum QuotationType: string
{
    case Standard = 'standard';
    case Detailed = 'detailed';
    case Revised = 'revised';
    case Express = 'express';

    public function label(): string
    {
        return match ($this) {
            self::Standard => 'Standard',
            self::Detailed => 'Detailed BOQ',
            self::Revised => 'Revision',
            self::Express => 'Express estimate',
        };
    }
}
