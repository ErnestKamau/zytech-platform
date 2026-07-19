<?php

namespace Tests\Unit\Core;

use App\Core\Data\ExampleDTO;
use App\Core\Enums\ProjectStatus;
use App\Core\Enums\QuotationStatus;
use App\Core\Helpers\SlugGenerator;
use App\Core\ValueObjects\Address;
use App\Core\ValueObjects\Coordinates;
use App\Core\ValueObjects\EmailAddress;
use App\Core\ValueObjects\Money;
use App\Core\ValueObjects\PhoneNumber;
use App\Infrastructure\Queue\QueueName;
use InvalidArgumentException;
use Tests\TestCase;

class CoreArchitectureTest extends TestCase
{
    public function test_project_status_enum_has_expected_cases(): void
    {
        $this->assertSame('published', ProjectStatus::Published->value);
        $this->assertSame('Published', ProjectStatus::Published->label());
    }

    public function test_quotation_status_enum_covers_lifecycle(): void
    {
        $this->assertContains(QuotationStatus::Pending, QuotationStatus::cases());
        $this->assertContains(QuotationStatus::Accepted, QuotationStatus::cases());
    }

    public function test_example_dto_round_trips_array(): void
    {
        $dto = ExampleDTO::fromArray([
            'name' => 'Zytech',
            'email' => 'hello@zytech.local',
        ]);

        $this->assertSame('Zytech', $dto->name);
        $this->assertSame([
            'name' => 'Zytech',
            'email' => 'hello@zytech.local',
        ], $dto->toArray());
    }

    public function test_money_value_object(): void
    {
        $money = Money::from(1500.5, 'kes');

        $this->assertSame('KES', $money->currency);
        $this->assertSame('1500.50', $money->amount);
        $this->assertTrue($money->equals(Money::from('1500.50', 'KES')));
    }

    public function test_email_address_rejects_invalid_values(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new EmailAddress('not-an-email');
    }

    public function test_phone_number_normalizes_whitespace(): void
    {
        $phone = new PhoneNumber('+254 712 345 678');

        $this->assertSame('+254712345678', $phone->value);
    }

    public function test_coordinates_validate_bounds(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Coordinates(120, 36.8);
    }

    public function test_address_string_representation(): void
    {
        $address = new Address(
            line1: 'Westlands',
            town: 'Nairobi',
            county: 'Nairobi',
        );

        $this->assertStringContainsString('Nairobi', (string) $address);
    }

    public function test_slug_generator(): void
    {
        $this->assertSame('riverside-apartments', SlugGenerator::make('Riverside Apartments'));
    }

    public function test_queue_names_are_defined(): void
    {
        $this->assertContains('mail', QueueName::all());
        $this->assertContains('media', QueueName::all());
        $this->assertContains('notifications', QueueName::all());
    }
}
