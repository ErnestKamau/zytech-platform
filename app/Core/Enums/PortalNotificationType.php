<?php

namespace App\Core\Enums;

enum PortalNotificationType: string
{
    case Quotation = 'quotation';
    case Project = 'project';
    case Document = 'document';
    case Message = 'message';
    case Meeting = 'meeting';
    case Support = 'support';
    case Announcement = 'announcement';
    case System = 'system';

    public function label(): string
    {
        return match ($this) {
            self::Quotation => 'Quotation',
            self::Project => 'Project',
            self::Document => 'Document',
            self::Message => 'Message',
            self::Meeting => 'Meeting',
            self::Support => 'Support',
            self::Announcement => 'Announcement',
            self::System => 'System',
        };
    }
}
