<?php

namespace App\Domains\Configuration\Data;

use App\Core\Data\BaseDTO;
use App\Core\Enums\NavigationLocation;

final readonly class NavigationData extends BaseDTO
{
    /**
     * @param  list<array{label: string, href: string, target: string, route_name: ?string}>  $items
     */
    public function __construct(
        public string $name,
        public NavigationLocation $location,
        public bool $isPublished,
        public array $items,
    ) {}

    public static function fromArray(array $data): static
    {
        $location = $data['location'] ?? NavigationLocation::Header;
        if (! $location instanceof NavigationLocation) {
            $location = NavigationLocation::from((string) $location);
        }

        return new self(
            name: (string) ($data['name'] ?? ''),
            location: $location,
            isPublished: (bool) ($data['is_published'] ?? false),
            items: is_array($data['items'] ?? null) ? $data['items'] : [],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'location' => $this->location->value,
            'is_published' => $this->isPublished,
            'items' => $this->items,
        ];
    }
}
