<?php

namespace App\Filament\Resources\NavigationMenus\Pages;

use App\Domains\Configuration\Actions\PublishNavigation;
use App\Filament\Resources\NavigationMenus\NavigationMenuResource;
use App\Models\NavigationMenu;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageNavigationMenus extends ManageRecords
{
    protected static string $resource = NavigationMenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(function (NavigationMenu $record): void {
                    if ($record->is_published) {
                        app(PublishNavigation::class)->handle($record);
                    }
                }),
        ];
    }
}
