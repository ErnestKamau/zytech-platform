<?php

namespace App\Core\Enums;

enum QuotationStatus: string
{
    case Pending = 'pending';
    case Reviewing = 'reviewing';
    case Preparing = 'preparing';
    case Sent = 'sent';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Expired = 'expired';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Reviewing => 'Reviewing',
            self::Preparing => 'Preparing',
            self::Sent => 'Sent',
            self::Accepted => 'Accepted',
            self::Rejected => 'Rejected',
            self::Expired => 'Expired',
            self::Completed => 'Completed',
        };
    }
}
