<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Domains\Configuration\Actions\ClearConfigurationCache;
use App\Domains\Configuration\Events\SettingsUpdated;
use App\Filament\Resources\Settings\SettingResource;
use App\Models\Setting;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSettings extends ManageRecords
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(function (Setting $record): void {
                    app(ClearConfigurationCache::class)->handle();
                    event(new SettingsUpdated([$record->key]));
                }),
        ];
    }
}
