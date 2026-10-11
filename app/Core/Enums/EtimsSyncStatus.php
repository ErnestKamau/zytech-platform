<?php

namespace App\Core\Enums;

enum EtimsSyncStatus: string
{
    case NotSynced = 'not_synced';
    case Pending = 'pending';
    case Synced = 'synced';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::NotSynced => 'Not synced',
            self::Pending => 'Pending',
            self::Synced => 'Synced',
            self::Failed => 'Failed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NotSynced => 'gray',
            self::Pending => 'warning',
            self::Synced => 'success',
            self::Failed => 'danger',
        };
    }
}
