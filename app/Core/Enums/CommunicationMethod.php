<?php

namespace App\Core\Enums;

enum CommunicationMethod: string
{
    case Email = 'email';
    case Phone = 'phone';
    case WhatsApp = 'whatsapp';
    case Meeting = 'meeting';
    case SiteVisit = 'site-visit';
    case Portal = 'portal';
    case InternalNote = 'internal-note';

    public function label(): string
    {
        return match ($this) {
            self::Email => 'Email',
            self::Phone => 'Phone call',
            self::WhatsApp => 'WhatsApp',
            self::Meeting => 'Meeting',
            self::SiteVisit => 'Site visit',
            self::Portal => 'Client portal',
            self::InternalNote => 'Internal note',
        };
    }
}
