<?php

namespace Tests\Unit\Communication;

use App\Domains\Communication\Services\TwilioWhatsAppService;
use RuntimeException;
use Tests\TestCase;

class TwilioWhatsAppServiceTest extends TestCase
{
    public function test_configured_is_false_when_whatsapp_from_is_missing(): void
    {
        config(['services.twilio.sid' => 'AC123', 'services.twilio.token' => 'secret', 'services.twilio.whatsapp_from' => null]);

        $this->assertFalse(app(TwilioWhatsAppService::class)->configured());
    }

    public function test_configured_is_true_when_all_credentials_present(): void
    {
        config(['services.twilio.sid' => 'AC123', 'services.twilio.token' => 'secret', 'services.twilio.whatsapp_from' => '+14155238886']);

        $this->assertTrue(app(TwilioWhatsAppService::class)->configured());
    }

    public function test_send_text_throws_when_unconfigured(): void
    {
        config(['services.twilio.sid' => null, 'services.twilio.token' => null, 'services.twilio.whatsapp_from' => null]);

        $this->expectException(RuntimeException::class);

        app(TwilioWhatsAppService::class)->sendText('+254712345678', 'hello');
    }

    public function test_template_sid_for_resolves_configured_mapping(): void
    {
        config(['services.twilio.whatsapp_templates.proforma-invoice-issued' => 'HX123456']);

        $this->assertSame('HX123456', app(TwilioWhatsAppService::class)->templateSidFor('proforma-invoice-issued'));
    }

    public function test_template_sid_for_returns_null_when_unmapped(): void
    {
        config(['services.twilio.whatsapp_templates.unknown-type' => null]);

        $this->assertNull(app(TwilioWhatsAppService::class)->templateSidFor('unknown-type'));
    }
}
