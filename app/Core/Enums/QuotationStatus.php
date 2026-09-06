<?php

namespace App\Core\Enums;

enum QuotationStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Reviewing = 'reviewing';
    case Preparing = 'preparing';
    case Sent = 'sent';
    case Viewed = 'viewed';
    case RevisionRequested = 'revision_requested';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Expired = 'expired';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Pending => 'Pending',
            self::Reviewing => 'Reviewing',
            self::Preparing => 'Preparing',
            self::Sent => 'Sent',
            self::Viewed => 'Viewed',
            self::RevisionRequested => 'Revision requested',
            self::Accepted => 'Accepted',
            self::Rejected => 'Rejected',
            self::Expired => 'Expired',
            self::Completed => 'Completed',
        };
    }
}
