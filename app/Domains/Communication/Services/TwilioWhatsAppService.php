<?php

namespace App\Domains\Communication\Services;

use App\Core\Services\BaseService;
use RuntimeException;
use Twilio\Rest\Client;

final class TwilioWhatsAppService extends BaseService
{
    /**
     * Freeform message. Only deliverable inside a 24h customer-initiated session window.
     */
    public function sendText(string $toE164, string $body): string
    {
        [$sid, $token, $from] = $this->credentials();

        $message = (new Client($sid, $token))->messages->create("whatsapp:{$toE164}", [
            'from' => "whatsapp:{$from}",
            'body' => $body,
        ]);

        return (string) $message->sid;
    }

    /**
     * Pre-approved Content API template. Required for any business-initiated send
     * outside the 24h session window — which is effectively every notification here.
     *
     * @param  array<string, string>  $contentVariables
     */
    public function sendTemplate(string $toE164, string $contentSid, array $contentVariables = []): string
    {
        [$sid, $token, $from] = $this->credentials();

        $message = (new Client($sid, $token))->messages->create("whatsapp:{$toE164}", [
            'from' => "whatsapp:{$from}",
            'contentSid' => $contentSid,
            'contentVariables' => json_encode($contentVariables),
        ]);

        return (string) $message->sid;
    }

    public function sendMedia(string $toE164, string $mediaUrl, ?string $caption = null): string
    {
        [$sid, $token, $from] = $this->credentials();

        $message = (new Client($sid, $token))->messages->create("whatsapp:{$toE164}", [
            'from' => "whatsapp:{$from}",
            'mediaUrl' => [$mediaUrl],
            'body' => $caption,
        ]);

        return (string) $message->sid;
    }

    public function configured(): bool
    {
        return filled(config('services.twilio.sid'))
            && filled(config('services.twilio.token'))
            && filled(config('services.twilio.whatsapp_from'));
    }

    public function templateSidFor(string $notificationType): ?string
    {
        $sid = config('services.twilio.whatsapp_templates.'.$notificationType);

        return filled($sid) ? (string) $sid : null;
    }

    /**
     * @return array{0: string, 1: string, 2: string}
     */
    private function credentials(): array
    {
        $sid = (string) config('services.twilio.sid');
        $token = (string) config('services.twilio.token');
        $from = (string) config('services.twilio.whatsapp_from');

        if ($sid === '' || $token === '' || $from === '') {
            throw new RuntimeException('Twilio WhatsApp is not configured. Set TWILIO_SID, TWILIO_AUTH_TOKEN, and TWILIO_WHATSAPP_FROM.');
        }

        return [$sid, $token, $from];
    }
}
