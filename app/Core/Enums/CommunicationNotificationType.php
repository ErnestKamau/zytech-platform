<?php

namespace App\Core\Enums;

enum CommunicationNotificationType: string
{
    case Welcome = 'welcome';
    case QuotationSubmitted = 'quotation-submitted';
    case QuotationSent = 'quotation-sent';
    case PortalMessage = 'portal-message';
    case SupportTicket = 'support-ticket';
    case MeetingScheduled = 'meeting-scheduled';
    case Announcement = 'announcement';
    case Generic = 'generic';

    public function label(): string
    {
        return match ($this) {
            self::Welcome => 'Welcome',
            self::QuotationSubmitted => 'Quotation submitted',
            self::QuotationSent => 'Quotation sent',
            self::PortalMessage => 'Portal message',
            self::SupportTicket => 'Support ticket',
            self::MeetingScheduled => 'Meeting scheduled',
            self::Announcement => 'Announcement',
            self::Generic => 'Generic',
        };
    }
}
