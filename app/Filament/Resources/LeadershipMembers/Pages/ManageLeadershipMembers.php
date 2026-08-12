<?php

namespace App\Filament\Resources\LeadershipMembers\Pages;

use App\Domains\Company\Services\CompanyService;
use App\Filament\Resources\LeadershipMembers\LeadershipMemberResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageLeadershipMembers extends ManageRecords
{
    protected static string $resource = LeadershipMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(fn () => app(CompanyService::class)->forget()),
        ];
    }
}
