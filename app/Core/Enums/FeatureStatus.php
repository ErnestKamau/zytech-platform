<?php

namespace App\Core\Enums;

enum FeatureStatus: string
{
    case Enabled = 'enabled';
    case Disabled = 'disabled';

    public function label(): string
    {
        return match ($this) {
            self::Enabled => 'Enabled',
            self::Disabled => 'Disabled',
        };
    }

    public function isEnabled(): bool
    {
        return $this === self::Enabled;
    }
}
