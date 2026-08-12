<?php

namespace App\Domains\Project\Data;

use App\Core\Data\BaseDTO;
use App\Core\Enums\MilestoneStatus;

final readonly class MilestoneData extends BaseDTO
{
    public function __construct(
        public string $title,
        public string $description,
        public MilestoneStatus $status,
        public int $sortOrder,
    ) {}

    public static function fromArray(array $data): static
    {
        $status = $data['status'] ?? MilestoneStatus::Pending;
        if (! $status instanceof MilestoneStatus) {
            $status = MilestoneStatus::from((string) $status);
        }

        return new self(
            title: (string) ($data['title'] ?? ''),
            description: (string) ($data['description'] ?? ''),
            status: $status,
            sortOrder: (int) ($data['sort_order'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status->value,
            'sort_order' => $this->sortOrder,
        ];
    }
}
