<?php

namespace App\Domains\Quotation\Data;

use App\Core\Data\BaseDTO;
use App\Core\Enums\BudgetRange;
use App\Core\Enums\PreferredContactMethod;
use App\Core\Enums\ProjectType;
use App\Core\Enums\QuotationStatus;

final readonly class QuotationRequestData extends BaseDTO
{
    /**
     * @param  list<string>  $serviceNames
     */
    public function __construct(
        public string $id,
        public string $referenceNumber,
        public string $fullName,
        public string $email,
        public ?string $phone,
        public ProjectType $projectType,
        public ?string $county,
        public ?string $location,
        public ?BudgetRange $budgetRange,
        public ?string $estimatedTimeline,
        public string $description,
        public PreferredContactMethod $preferredContactMethod,
        public QuotationStatus $status,
        public array $serviceNames,
        public ?string $submittedAt,
    ) {}

    public static function fromArray(array $data): static
    {
        $projectType = $data['project_type'] ?? ProjectType::Residential;
        if (! $projectType instanceof ProjectType) {
            $projectType = ProjectType::from((string) $projectType);
        }

        $contact = $data['preferred_contact_method'] ?? PreferredContactMethod::Email;
        if (! $contact instanceof PreferredContactMethod) {
            $contact = PreferredContactMethod::from((string) $contact);
        }

        $status = $data['status'] ?? QuotationStatus::Pending;
        if (! $status instanceof QuotationStatus) {
            $status = QuotationStatus::from((string) $status);
        }

        $budget = $data['budget_range'] ?? null;
        if ($budget !== null && ! $budget instanceof BudgetRange) {
            $budget = BudgetRange::from((string) $budget);
        }

        return new self(
            id: (string) ($data['id'] ?? ''),
            referenceNumber: (string) ($data['reference_number'] ?? ''),
            fullName: (string) ($data['full_name'] ?? ''),
            email: (string) ($data['email'] ?? ''),
            phone: isset($data['phone']) ? (string) $data['phone'] : null,
            projectType: $projectType,
            county: isset($data['county']) ? (string) $data['county'] : null,
            location: isset($data['location']) ? (string) $data['location'] : null,
            budgetRange: $budget,
            estimatedTimeline: isset($data['estimated_timeline']) ? (string) $data['estimated_timeline'] : null,
            description: (string) ($data['description'] ?? ''),
            preferredContactMethod: $contact,
            status: $status,
            serviceNames: array_values($data['service_names'] ?? []),
            submittedAt: isset($data['submitted_at']) ? (string) $data['submitted_at'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'reference_number' => $this->referenceNumber,
            'full_name' => $this->fullName,
            'email' => $this->email,
            'phone' => $this->phone,
            'project_type' => $this->projectType->value,
            'county' => $this->county,
            'location' => $this->location,
            'budget_range' => $this->budgetRange?->value,
            'estimated_timeline' => $this->estimatedTimeline,
            'description' => $this->description,
            'preferred_contact_method' => $this->preferredContactMethod->value,
            'status' => $this->status->value,
            'service_names' => $this->serviceNames,
            'submitted_at' => $this->submittedAt,
        ];
    }
}
