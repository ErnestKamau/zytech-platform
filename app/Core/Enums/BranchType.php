<?php

namespace App\Core\Enums;

enum BranchType: string
{
    case Headquarters = 'headquarters';
    case Branch = 'branch';
    case SiteOffice = 'site_office';

    public function label(): string
    {
        return match ($this) {
            self::Headquarters => 'Headquarters',
            self::Branch => 'Branch',
            self::SiteOffice => 'Site office',
        };
    }
}
