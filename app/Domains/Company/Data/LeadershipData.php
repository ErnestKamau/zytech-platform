<?php

namespace App\Domains\Company\Data;

use App\Core\Data\BaseDTO;

final readonly class LeadershipData extends BaseDTO
{
    public function __construct(
        public string $name,
        public string $position,
        public ?string $biography,
        public ?string $photoUrl,
        public ?string $email,
        public ?string $linkedinUrl,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            name: (string) $data['name'],
            position: (string) $data['position'],
            biography: isset($data['biography']) ? (string) $data['biography'] : null,
            photoUrl: isset($data['photo_url']) ? (string) $data['photo_url'] : null,
            email: isset($data['email']) ? (string) $data['email'] : null,
            linkedinUrl: isset($data['linkedin_url']) ? (string) $data['linkedin_url'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'position' => $this->position,
            'biography' => $this->biography,
            'photo_url' => $this->photoUrl,
            'email' => $this->email,
            'linkedin_url' => $this->linkedinUrl,
        ];
    }
}
