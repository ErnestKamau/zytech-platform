<?php

namespace App\Domains\Media\Data;

use App\Core\Data\BaseDTO;
use App\Core\Enums\ConversionType;

final readonly class MediaConversionData extends BaseDTO
{
    public function __construct(
        public ConversionType $type,
        public int $width,
        public bool $generated,
    ) {}

    public static function fromArray(array $data): static
    {
        $type = $data['type'] ?? ConversionType::Thumb->value;

        return new self(
            type: $type instanceof ConversionType ? $type : ConversionType::from((string) $type),
            width: (int) ($data['width'] ?? 0),
            generated: (bool) ($data['generated'] ?? false),
        );
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'width' => $this->width,
            'generated' => $this->generated,
        ];
    }
}
