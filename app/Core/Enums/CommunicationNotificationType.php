<?php

namespace App\Core\Enums;

enum CommunicationNotificationType: string
{
    case Welcome = 'welcome';
    case QuotationSubmitted = 'quotation-submitted';
    case QuotationSent = 'quotation-sent';
    case QuotationAccepted = 'quotation-accepted';
    case QuotationRejected = 'quotation-rejected';
    case OrderPlaced = 'order-placed';
    case OrderCancelled = 'order-cancelled';
    case OrderStatusChanged = 'order-status-changed';
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
            self::QuotationAccepted => 'Quotation accepted',
            self::QuotationRejected => 'Quotation rejected',
            self::OrderPlaced => 'Order placed',
            self::OrderCancelled => 'Order cancelled',
            self::OrderStatusChanged => 'Order status changed',
            self::PortalMessage => 'Portal message',
            self::SupportTicket => 'Support ticket',
            self::MeetingScheduled => 'Meeting scheduled',
            self::Announcement => 'Announcement',
            self::Generic => 'Generic',
        };
    }
}
