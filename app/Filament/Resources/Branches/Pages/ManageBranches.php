<?php

namespace App\Filament\Resources\Branches\Pages;

use App\Domains\Company\Services\BranchService;
use App\Filament\Resources\Branches\BranchResource;
use App\Models\Branch;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBranches extends ManageRecords
{
    protected static string $resource = BranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->using(fn (array $data): Branch => app(BranchService::class)->create($data)),
        ];
    }
}
