<?php

namespace App\Domains\Communication\Services;

use App\Core\Enums\DeliveryStatus;
use App\Core\Enums\NotificationChannel;
use App\Core\Services\BaseService;
use App\Domains\Communication\Events\NotificationDispatched;
use App\Domains\Communication\Events\NotificationPushed;
use App\Domains\Communication\Mail\TemplatedMail;
use App\Domains\Communication\Notifications\HubDatabaseNotification;
use App\Models\NotificationLog;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

final class CommunicationService extends BaseService
{
    public function __construct(
        private readonly TemplateService $templates,
        private readonly ActivityFeedService $feed,
    ) {}

    /**
     * @param  array<string, string>  $replacements
     * @param  list<NotificationChannel>|null  $channels
     * @param  array<string, mixed>|null  $meta
     */
    public function notify(
        string $type,
        string $recipientEmail,
        ?User $user = null,
        ?string $templateKey = null,
        array $replacements = [],
        ?array $channels = null,
        ?array $meta = null,
        ?string $subject = null,
        ?string $body = null,
    ): void {
        $channels ??= [NotificationChannel::Mail, NotificationChannel::Database, NotificationChannel::Broadcast];
        $preferences = $user !== null ? $this->preferencesFor($user) : null;

        $template = $templateKey !== null ? $this->templates->findByKey($templateKey) : null;
        $resolvedSubject = $subject
            ?? ($template !== null ? $this->templates->render($template->subject, $replacements) : ucfirst(str_replace('-', ' ', $type)));
        $resolvedBody = $body
            ?? ($template !== null ? $this->templates->render($template->body, $replacements) : ($replacements['message'] ?? $resolvedSubject));

        foreach ($channels as $channel) {
            if ($preferences !== null && ! $this->channelAllowed($preferences, $channel)) {
                $this->log($type, $channel, DeliveryStatus::Skipped, $recipientEmail, $user, $resolvedSubject, $resolvedBody, $meta, 'Disabled by preference');

                continue;
            }

            try {
                match ($channel) {
                    NotificationChannel::Mail => $this->sendMail($recipientEmail, $resolvedSubject, $resolvedBody),
                    NotificationChannel::Database => $this->sendDatabase($user, $type, $resolvedSubject, $resolvedBody, $meta),
                    NotificationChannel::Broadcast => $this->sendBroadcast($type, $resolvedSubject, $resolvedBody, $user, $meta),
                    NotificationChannel::Portal => null,
                };

                $this->log($type, $channel, DeliveryStatus::Sent, $recipientEmail, $user, $resolvedSubject, $resolvedBody, $meta);
            } catch (Throwable $exception) {
                $this->log(
                    $type,
                    $channel,
                    DeliveryStatus::Failed,
                    $recipientEmail,
                    $user,
                    $resolvedSubject,
                    $resolvedBody,
                    $meta,
                    $exception->getMessage(),
                );

                Log::error('communication.dispatch.failed', [
                    'type' => $type,
                    'channel' => $channel->value,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $this->feed->record($user, $type, $resolvedSubject, $resolvedBody, $meta);

        event(new NotificationDispatched(
            type: $type,
            recipient: $recipientEmail,
            subject: $resolvedSubject,
        ));
    }

    public function preferencesFor(User $user): NotificationPreference
    {
        return NotificationPreference::query()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'mail_enabled' => true,
                'database_enabled' => true,
                'broadcast_enabled' => true,
                'marketing_enabled' => false,
            ],
        );
    }

    private function channelAllowed(NotificationPreference $preferences, NotificationChannel $channel): bool
    {
        return match ($channel) {
            NotificationChannel::Mail => $preferences->mail_enabled,
            NotificationChannel::Database => $preferences->database_enabled,
            NotificationChannel::Broadcast => $preferences->broadcast_enabled,
            NotificationChannel::Portal => true,
        };
    }

    private function sendMail(string $email, string $subject, string $body): void
    {
        Mail::mailer(config('mail.default'))->to($email)->send(new TemplatedMail($subject, $body));
    }

    /**
     * @param  array<string, mixed>|null  $meta
     */
    private function sendDatabase(?User $user, string $type, string $subject, string $body, ?array $meta): void
    {
        if ($user === null) {
            return;
        }

        $user->notify(new HubDatabaseNotification($type, $subject, $body, $meta ?? []));
    }

    /**
     * @param  array<string, mixed>|null  $meta
     */
    private function sendBroadcast(
        string $type,
        string $subject,
        string $body,
        ?User $user,
        ?array $meta,
    ): void {
        event(new NotificationPushed(
            type: $type,
            title: $subject,
            body: $body,
            userId: $user?->id,
            meta: $meta,
        ));

        Log::info('communication.broadcast', [
            'type' => $type,
            'subject' => $subject,
            'user_id' => $user?->id,
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $meta
     */
    private function log(
        string $type,
        NotificationChannel $channel,
        DeliveryStatus $status,
        string $recipient,
        ?User $user,
        string $subject,
        string $body,
        ?array $meta,
        ?string $error = null,
    ): void {
        NotificationLog::query()->create([
            'type' => $type,
            'channel' => $channel,
            'status' => $status,
            'recipient' => $recipient,
            'user_id' => $user?->id,
            'subject' => $subject,
            'body' => $body,
            'meta' => $meta,
            'error' => $error,
            'sent_at' => $status === DeliveryStatus::Sent ? now() : null,
        ]);
    }
}
