<?php

namespace App\Core\Enums;

enum BudgetRange: string
{
    case Under1M = 'under-1m';
    case OneToFiveM = '1m-5m';
    case FiveToFifteenM = '5m-15m';
    case FifteenToFiftyM = '15m-50m';
    case OverFiftyM = 'over-50m';
    case Undecided = 'undecided';

    public function label(): string
    {
        return match ($this) {
            self::Under1M => 'Under KES 1M',
            self::OneToFiveM => 'KES 1M – 5M',
            self::FiveToFifteenM => 'KES 5M – 15M',
            self::FifteenToFiftyM => 'KES 15M – 50M',
            self::OverFiftyM => 'Over KES 50M',
            self::Undecided => 'Not sure yet',
        };
    }
}
