<?php

namespace App\Domains\Configuration\Data;

use App\Core\Data\BaseDTO;

final readonly class BrandingData extends BaseDTO
{
    public function __construct(
        public string $companyName,
        public string $shortName,
        public string $tagline,
        public string $description,
        public ?string $logoUrl = null,
        public ?string $primaryColor = null,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            companyName: (string) ($data['company.name'] ?? $data['company_name'] ?? 'Zytech Contractors'),
            shortName: (string) ($data['company.short_name'] ?? $data['short_name'] ?? 'Zytech'),
            tagline: (string) ($data['company.tagline'] ?? $data['tagline'] ?? ''),
            description: (string) ($data['company.description'] ?? $data['description'] ?? ''),
            logoUrl: isset($data['branding.logo_url']) ? (string) $data['branding.logo_url'] : ($data['logo_url'] ?? null),
            primaryColor: isset($data['branding.primary_color']) ? (string) $data['branding.primary_color'] : ($data['primary_color'] ?? null),
        );
    }

    public function toArray(): array
    {
        return [
            'company.name' => $this->companyName,
            'company.short_name' => $this->shortName,
            'company.tagline' => $this->tagline,
            'company.description' => $this->description,
            'branding.logo_url' => $this->logoUrl,
            'branding.primary_color' => $this->primaryColor,
        ];
    }
}
