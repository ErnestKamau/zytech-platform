<?php

namespace App\Core\Enums;

enum ClientTimelineEvent: string
{
    case LeadCreated = 'lead-created';
    case QuotationRequested = 'quotation-requested';
    case QuotationSent = 'quotation-sent';
    case QuotationAccepted = 'quotation-accepted';
    case ProjectStarted = 'project-started';
    case ProjectCompleted = 'project-completed';
    case DocumentUploaded = 'document-uploaded';
    case MeetingScheduled = 'meeting-scheduled';
    case CommunicationLogged = 'communication-logged';
    case PortalAccessGranted = 'portal-access-granted';
    case NoteAdded = 'note-added';
    case StatusChanged = 'status-changed';

    public function label(): string
    {
        return match ($this) {
            self::LeadCreated => 'Lead created',
            self::QuotationRequested => 'Quotation requested',
            self::QuotationSent => 'Quotation sent',
            self::QuotationAccepted => 'Quotation accepted',
            self::ProjectStarted => 'Project started',
            self::ProjectCompleted => 'Project completed',
            self::DocumentUploaded => 'Document uploaded',
            self::MeetingScheduled => 'Meeting scheduled',
            self::CommunicationLogged => 'Communication logged',
            self::PortalAccessGranted => 'Portal access granted',
            self::NoteAdded => 'Note added',
            self::StatusChanged => 'Status changed',
        };
    }
}
