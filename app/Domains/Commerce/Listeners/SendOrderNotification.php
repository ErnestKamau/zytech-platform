<?php

namespace App\Domains\Commerce\Listeners;

use App\Core\Enums\CommunicationNotificationType;
use App\Core\Enums\NotificationChannel;
use App\Core\Enums\OrderStatus;
use App\Core\Enums\RoleType;
use App\Core\Listeners\BaseListener;
use App\Domains\Commerce\Events\OrderCancelled;
use App\Domains\Commerce\Events\OrderPlaced;
use App\Domains\Commerce\Events\OrderStatusChanged;
use App\Domains\Communication\Services\CommunicationService;
use App\Infrastructure\Queue\QueueName;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

final class SendOrderNotification extends BaseListener
{
    public string $queue = QueueName::MAIL;

    /** @var list<OrderStatus> */
    private const CUSTOMER_STATUS_EMAILS = [
        OrderStatus::Confirmed,
        OrderStatus::ReadyForFulfillment,
        OrderStatus::Completed,
    ];

    public function __construct(private readonly CommunicationService $communication) {}

    public function handle(OrderPlaced|OrderCancelled|OrderStatusChanged $event): void
    {
        if ($event instanceof OrderPlaced) {
            $this->notifyCustomerPlaced($event->order);
            $this->notifyStaff(
                $event->order,
                CommunicationNotificationType::OrderPlaced->value,
                'New order '.$event->order->order_number,
                'A customer placed order '.$event->order->order_number.'.',
            );

            return;
        }

        if ($event instanceof OrderCancelled) {
            $this->notifyCustomerCancelled($event->order);
            $this->notifyStaff(
                $event->order,
                CommunicationNotificationType::OrderCancelled->value,
                'Order '.$event->order->order_number.' cancelled',
                'Order '.$event->order->order_number.' was cancelled.',
            );

            return;
        }

        if (! in_array($event->to, self::CUSTOMER_STATUS_EMAILS, true)) {
            return;
        }

        $this->notifyCustomerStatusChanged($event->order, $event->to);
    }

    private function notifyCustomerPlaced(Order $order): void
    {
        $email = $order->contact_email;
        if ($email === null || $email === '') {
            return;
        }

        $order->loadMissing('client.user');

        $this->communication->notify(
            type: CommunicationNotificationType::OrderPlaced->value,
            recipientEmail: $email,
            user: $order->client?->user,
            client: $order->client,
            templateKey: 'order-placed',
            replacements: [
                'name' => (string) ($order->contact_name ?? $order->client?->name ?? 'there'),
                'reference' => (string) $order->order_number,
                'total' => number_format((float) $order->total_amount, 2).' '.($order->currency ?: 'KES'),
                'message' => 'We will confirm your order shortly.',
            ],
            channels: [NotificationChannel::Mail, NotificationChannel::Database],
            meta: [
                'order_id' => $order->id,
                'client_id' => $order->client_id,
            ],
        );
    }

    private function notifyCustomerCancelled(Order $order): void
    {
        $email = $order->contact_email;
        if ($email === null || $email === '') {
            return;
        }

        $order->loadMissing('client.user');

        $this->communication->notify(
            type: CommunicationNotificationType::OrderCancelled->value,
            recipientEmail: $email,
            user: $order->client?->user,
            client: $order->client,
            templateKey: 'order-cancelled',
            replacements: [
                'name' => (string) ($order->contact_name ?? $order->client?->name ?? 'there'),
                'reference' => (string) $order->order_number,
                'message' => 'If this was unexpected, contact Zytech support.',
            ],
            channels: [NotificationChannel::Mail, NotificationChannel::Database],
            meta: [
                'order_id' => $order->id,
                'client_id' => $order->client_id,
            ],
        );
    }

    private function notifyCustomerStatusChanged(Order $order, OrderStatus $status): void
    {
        $email = $order->contact_email;
        if ($email === null || $email === '') {
            return;
        }

        $order->loadMissing('client.user');

        $this->communication->notify(
            type: CommunicationNotificationType::OrderStatusChanged->value,
            recipientEmail: $email,
            user: $order->client?->user,
            client: $order->client,
            templateKey: 'order-status-changed',
            replacements: [
                'name' => (string) ($order->contact_name ?? $order->client?->name ?? 'there'),
                'reference' => (string) $order->order_number,
                'status' => $status->label(),
                'message' => 'Track updates in your account portal.',
            ],
            channels: [NotificationChannel::Mail, NotificationChannel::Database],
            meta: [
                'order_id' => $order->id,
                'client_id' => $order->client_id,
                'status' => $status->value,
            ],
        );
    }

    private function notifyStaff(Order $order, string $type, string $subject, string $message): void
    {
        $order->loadMissing('client');

        foreach ($this->staffRecipients($order) as $user) {
            if ($user->email === null || $user->email === '') {
                continue;
            }

            $this->communication->notify(
                type: $type,
                recipientEmail: $user->email,
                user: $user,
                templateKey: null,
                replacements: [
                    'name' => $user->name,
                    'reference' => (string) $order->order_number,
                    'message' => $message,
                ],
                channels: [NotificationChannel::Database],
                meta: [
                    'order_id' => $order->id,
                    'client_id' => $order->client_id,
                ],
                subject: $subject,
                body: $message,
            );
        }
    }

    /**
     * @return Collection<int, User>
     */
    private function staffRecipients(Order $order): Collection
    {
        $recipients = collect();

        if ($order->client?->assigned_sales_id) {
            $assigned = User::query()->find($order->client->assigned_sales_id);
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
