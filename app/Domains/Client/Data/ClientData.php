<?php

namespace App\Domains\Client\Data;

use App\Core\Data\BaseDTO;
use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\PreferredContactMethod;

final readonly class ClientData extends BaseDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public ?string $phone,
        public ClientType $type,
        public ClientStatus $status,
        public PreferredContactMethod $preferredContactMethod,
        public ?string $industry,
        public ?string $assignedSalesName,
        public int $quotationCount,
        public int $projectCount,
    ) {}

    public static function fromArray(array $data): static
    {
        $type = $data['type'] ?? ClientType::Individual;
        if (! $type instanceof ClientType) {
            $type = ClientType::from((string) $type);
        }

        $status = $data['status'] ?? ClientStatus::Prospect;
        if (! $status instanceof ClientStatus) {
            $status = ClientStatus::from((string) $status);
        }

        $contact = $data['preferred_contact_method'] ?? PreferredContactMethod::Email;
        if (! $contact instanceof PreferredContactMethod) {
            $contact = PreferredContactMethod::from((string) $contact);
        }

        return new self(
            id: (string) ($data['id'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            email: (string) ($data['email'] ?? ''),
            phone: isset($data['phone']) ? (string) $data['phone'] : null,
            type: $type,
            status: $status,
            preferredContactMethod: $contact,
            industry: isset($data['industry']) ? (string) $data['industry'] : null,
            assignedSalesName: isset($data['assigned_sales_name']) ? (string) $data['assigned_sales_name'] : null,
            quotationCount: (int) ($data['quotation_count'] ?? 0),
            projectCount: (int) ($data['project_count'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'type' => $this->type->value,
            'status' => $this->status->value,
            'preferred_contact_method' => $this->preferredContactMethod->value,
            'industry' => $this->industry,
            'assigned_sales_name' => $this->assignedSalesName,
            'quotation_count' => $this->quotationCount,
            'project_count' => $this->projectCount,
        ];
    }
}
