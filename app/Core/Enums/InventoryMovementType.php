<?php

namespace App\Core\Enums;

enum InventoryMovementType: string
{
    case In = 'in';
    case Out = 'out';
    case Adjust = 'adjust';
    case Reserve = 'reserve';
    case Release = 'release';

    public function label(): string
    {
        return match ($this) {
            self::In => 'Stock in',
            self::Out => 'Stock out',
            self::Adjust => 'Adjustment',
            self::Reserve => 'Reserved',
            self::Release => 'Released',
        };
    }
}
