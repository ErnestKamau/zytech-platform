<?php

namespace App\Core\ValueObjects;

use Stringable;

final readonly class Address implements Stringable
{
    public function __construct(
        public string $line1,
        public ?string $line2 = null,
        public ?string $town = null,
        public ?string $county = null,
        public string $country = 'Kenya',
        public ?string $postalCode = null,
    ) {}

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'line1' => $this->line1,
            'line2' => $this->line2,
            'town' => $this->town,
            'county' => $this->county,
            'country' => $this->country,
            'postal_code' => $this->postalCode,
        ];
    }

    public function __toString(): string
    {
        return collect([
            $this->line1,
            $this->line2,
            $this->town,
            $this->county,
            $this->country,
            $this->postalCode,
        ])->filter()->implode(', ');
    }
}
