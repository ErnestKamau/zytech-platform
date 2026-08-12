<?php

namespace App\Domains\Company\Data;

use App\Core\Data\BaseDTO;

final readonly class CompanyData extends BaseDTO
{
    /**
     * @param  list<string>  $coreValues
     */
    public function __construct(
        public string $name,
        public string $tagline,
        public string $motto,
        public string $shortDescription,
        public string $about,
        public string $mission,
        public string $vision,
        public string $history,
        public string $whyChooseUs,
        public array $coreValues,
        public string $email,
        public string $phone,
        public string $whatsapp,
        public string $location,
        public string $serviceArea,
        public bool $isPublished,
    ) {}

    public static function fromArray(array $data): static
    {
        $values = $data['core_values'] ?? [];

        return new self(
            name: (string) ($data['name'] ?? ''),
            tagline: (string) ($data['tagline'] ?? ''),
            motto: (string) ($data['motto'] ?? ''),
            shortDescription: (string) ($data['short_description'] ?? ''),
            about: (string) ($data['about'] ?? ''),
            mission: (string) ($data['mission'] ?? ''),
            vision: (string) ($data['vision'] ?? ''),
            history: (string) ($data['history'] ?? ''),
            whyChooseUs: (string) ($data['why_choose_us'] ?? ''),
            coreValues: is_array($values) ? array_values(array_map('strval', $values)) : [],
            email: (string) ($data['email'] ?? ''),
            phone: (string) ($data['phone'] ?? ''),
            whatsapp: (string) ($data['whatsapp'] ?? ''),
            location: (string) ($data['location'] ?? ''),
            serviceArea: (string) ($data['service_area'] ?? ''),
            isPublished: (bool) ($data['is_published'] ?? false),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'tagline' => $this->tagline,
            'motto' => $this->motto,
            'short_description' => $this->shortDescription,
            'about' => $this->about,
            'mission' => $this->mission,
            'vision' => $this->vision,
            'history' => $this->history,
            'why_choose_us' => $this->whyChooseUs,
            'core_values' => $this->coreValues,
            'email' => $this->email,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'location' => $this->location,
            'service_area' => $this->serviceArea,
            'is_published' => $this->isPublished,
        ];
    }
}
