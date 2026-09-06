<?php

namespace App\Core\Enums;

enum PurchaseOrderStatus: string
{
    case NotRequired = 'not_required';
    case Awaiting = 'awaiting';
    case Uploaded = 'uploaded';
    case UnderReview = 'under_review';
    case Accepted = 'accepted';
    case Mismatch = 'mismatch';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::NotRequired => 'Not required',
            self::Awaiting => 'Awaiting PO',
            self::Uploaded => 'Uploaded',
            self::UnderReview => 'Under review',
            self::Accepted => 'Accepted',
            self::Mismatch => 'Mismatch',
            self::Rejected => 'Rejected',
        };
    }
}
