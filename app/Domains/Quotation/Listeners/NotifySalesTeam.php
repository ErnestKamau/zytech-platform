<?php

namespace App\Domains\Quotation\Listeners;

use App\Core\Enums\CommunicationNotificationType;
use App\Core\Enums\NotificationChannel;
use App\Core\Enums\RoleType;
use App\Core\Listeners\BaseListener;
use App\Domains\Communication\Services\CommunicationService;
use App\Domains\Quotation\Events\LeadCreated;
use App\Domains\Quotation\Events\LeadQualified;
use App\Domains\Quotation\Events\QuotationAccepted;
use App\Domains\Quotation\Events\QuotationApproved;
use App\Domains\Quotation\Events\QuotationCreated;
use App\Domains\Quotation\Events\QuotationRejected;
use App\Domains\Quotation\Events\QuotationRequestSubmitted;
use App\Domains\Quotation\Events\QuotationRevisionRequested;
use App\Domains\Quotation\Events\QuotationSent;
use App\Domains\Quotation\Events\SiteVisitScheduled;
use App\Infrastructure\Queue\QueueName;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

final class NotifySalesTeam extends BaseListener
{
    public string $queue = QueueName::NOTIFICATIONS;

    public function __construct(private readonly CommunicationService $communication) {}

    public function handle(
        QuotationRequestSubmitted|LeadCreated|LeadQualified|QuotationCreated|QuotationApproved|QuotationSent|QuotationAccepted|QuotationRejected|QuotationRevisionRequested|SiteVisitScheduled $event,
    ): void {
        $context = $this->contextFor($event);

        foreach ($this->salesRecipients($context['client']) as $user) {
            if ($user->email === null || $user->email === '') {
                continue;
            }

            $this->communication->notify(
                type: $context['type'],
                recipientEmail: $user->email,
                user: $user,
                templateKey: null,
                replacements: [
                    'name' => $user->name,
                    'reference' => $context['reference'],
                    'message' => $context['message'],
                ],
                channels: [NotificationChannel::Database],
                meta: $context['meta'],
                subject: $context['subject'],
                body: $context['message'],
            );
        }
    }

    /**
     * @return array{type: string, subject: string, message: string, reference: string, client: ?Client, meta: array<string, mixed>}
     */
    private function contextFor(object $event): array
    {
        return match (true) {
            $event instanceof QuotationRequestSubmitted => [
                'type' => CommunicationNotificationType::QuotationSubmitted->value,
                'subject' => 'New quotation request '.$event->request->reference_number,
                'message' => 'A new RFQ requires review.',
                'reference' => (string) $event->request->reference_number,
                'client' => $event->request->client,
                'meta' => ['quotation_request_id' => $event->request->id, 'client_id' => $event->request->client_id],
            ],
            $event instanceof QuotationAccepted => [
                'type' => CommunicationNotificationType::QuotationAccepted->value,
                'subject' => 'Quotation '.$event->quotation->reference_number.' accepted',
                'message' => 'The customer accepted quotation '.$event->quotation->reference_number.'.',
                'reference' => (string) $event->quotation->reference_number,
                'client' => $event->quotation->client,
                'meta' => ['quotation_id' => $event->quotation->id, 'client_id' => $event->quotation->client_id],
            ],
            $event instanceof QuotationRejected => [
                'type' => CommunicationNotificationType::QuotationRejected->value,
                'subject' => 'Quotation '.$event->quotation->reference_number.' rejected',
                'message' => 'The customer rejected quotation '.$event->quotation->reference_number.'.',
                'reference' => (string) $event->quotation->reference_number,
                'client' => $event->quotation->client,
                'meta' => ['quotation_id' => $event->quotation->id, 'client_id' => $event->quotation->client_id],
            ],
            $event instanceof QuotationSent => [
                'type' => CommunicationNotificationType::QuotationSent->value,
                'subject' => 'Quotation '.$event->quotation->reference_number.' sent',
                'message' => 'Quotation '.$event->quotation->reference_number.' was sent to the customer.',
                'reference' => (string) $event->quotation->reference_number,
                'client' => $event->quotation->client,
                'meta' => ['quotation_id' => $event->quotation->id, 'client_id' => $event->quotation->client_id],
            ],
            $event instanceof QuotationCreated => [
                'type' => CommunicationNotificationType::Generic->value,
                'subject' => 'Quotation '.$event->quotation->reference_number.' drafted',
                'message' => 'A quotation draft was created.',
                'reference' => (string) $event->quotation->reference_number,
                'client' => $event->quotation->client,
                'meta' => ['quotation_id' => $event->quotation->id, 'client_id' => $event->quotation->client_id],
            ],
            $event instanceof QuotationApproved => [
                'type' => CommunicationNotificationType::Generic->value,
                'subject' => 'Quotation '.$event->quotation->reference_number.' approved internally',
                'message' => 'Internal approval recorded for '.$event->quotation->reference_number.'.',
                'reference' => (string) $event->quotation->reference_number,
                'client' => $event->quotation->client,
                'meta' => ['quotation_id' => $event->quotation->id, 'client_id' => $event->quotation->client_id],
            ],
            $event instanceof QuotationRevisionRequested => [
                'type' => CommunicationNotificationType::Generic->value,
                'subject' => 'Revision requested for '.$event->quotation->reference_number,
                'message' => 'The customer requested a revision.',
                'reference' => (string) $event->quotation->reference_number,
                'client' => $event->quotation->client,
                'meta' => ['quotation_id' => $event->quotation->id, 'client_id' => $event->quotation->client_id],
            ],
            $event instanceof LeadCreated => [
                'type' => CommunicationNotificationType::Generic->value,
                'subject' => 'New lead: '.$event->lead->full_name,
                'message' => 'A new sales lead was created.',
                'reference' => (string) ($event->lead->email ?? $event->lead->id),
                'client' => $event->lead->client,
                'meta' => ['sales_lead_id' => $event->lead->id, 'client_id' => $event->lead->client_id],
            ],
            $event instanceof LeadQualified => [
                'type' => CommunicationNotificationType::Generic->value,
                'subject' => 'Lead qualified: '.$event->lead->full_name,
                'message' => 'A sales lead was marked qualified.',
                'reference' => (string) ($event->lead->email ?? $event->lead->id),
                'client' => $event->lead->client,
                'meta' => ['sales_lead_id' => $event->lead->id, 'client_id' => $event->lead->client_id],
            ],
            $event instanceof SiteVisitScheduled => [
                'type' => CommunicationNotificationType::Generic->value,
                'subject' => 'Site visit scheduled',
                'message' => 'A site visit was scheduled.',
                'reference' => (string) $event->visit->id,
                'client' => $event->visit->quotationRequest?->client ?? $event->visit->salesLead?->client,
                'meta' => ['site_visit_id' => $event->visit->id],
            ],
        };
    }

    /**
     * @return Collection<int, User>
     */
    private function salesRecipients(?Client $client): Collection
    {
        $recipients = collect();

        if ($client?->assigned_sales_id) {
            $assigned = User::query()->find($client->assigned_sales_id);
            if ($assigned !== null) {
                $recipients->push($assigned);
            }
        }

        if ($recipients->isEmpty()) {
            $roleNames = [
                RoleType::Staff->value,
                RoleType::Administrator->value,
                RoleType::SuperAdmin->value,
            ];

            $existingRoles = Role::query()
                ->whereIn('name', $roleNames)
                ->pluck('name')
                ->all();

            if ($existingRoles !== []) {
                $recipients = User::role($existingRoles)->get();
            }
        }

        return $recipients->unique('id')->values();
    }
}
