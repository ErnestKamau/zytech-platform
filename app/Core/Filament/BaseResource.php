<?php

namespace App\Core\Filament;

use Filament\Resources\Resource;

abstract class BaseResource extends Resource
{
    // UI only — business rules belong in domain Services / Actions / Policies.
}
