<?php

namespace App\Domains\Communication\Services;

use App\Core\Services\BaseService;
use RuntimeException;
use Twilio\Rest\Client;

final class TwilioSmsService extends BaseService
{
    public function send(string $toE164, string $body): string
    {
        $sid = (string) config('services.twilio.sid');
        $token = (string) config('services.twilio.token');
        $from = (string) config('services.twilio.from');

        if ($sid === '' || $token === '' || $from === '') {
            throw new RuntimeException('Twilio is not configured. Set TWILIO_SID, TWILIO_AUTH_TOKEN, and TWILIO_FROM.');
        }

        $message = (new Client($sid, $token))->messages->create($toE164, [
            'from' => $from,
            'body' => $body,
        ]);

        return (string) $message->sid;
    }

    public function configured(): bool
    {
        return filled(config('services.twilio.sid'))
            && filled(config('services.twilio.token'))
            && filled(config('services.twilio.from'));
    }
}
