<?php

namespace Tests\Unit\Communication;

use App\Domains\Communication\Support\PhoneNumber;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PhoneNumberTest extends TestCase
{
    /**
     * @return array<string, array{0: string, 1: ?string}>
     */
    public static function phoneProvider(): array
    {
        return [
            'local 07xx' => ['0712345678', '+254712345678'],
            'local 01xx' => ['0112345678', '+254112345678'],
            'plus e164' => ['+254712345678', '+254712345678'],
            'bare 254' => ['254712345678', '+254712345678'],
            'bare 9-digit' => ['712345678', '+254712345678'],
            'spaces and dashes' => ['0712-345-678', '+254712345678'],
            'too short' => ['12345', null],
            'invalid prefix' => ['0812345678', null],
            'empty string' => ['', null],
            'null' => [null, null],
        ];
    }

    #[DataProvider('phoneProvider')]
    public function test_normalizes_kenyan_phone_numbers(?string $input, ?string $expected): void
    {
        $this->assertSame($expected, PhoneNumber::toE164Kenyan($input));
    }
}
