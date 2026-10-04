<?php

namespace App\Filament\Resources\HomepageSlides\Pages;

use App\Domains\Homepage\Actions\SyncHomepageSlideMedia;
use App\Domains\Homepage\Services\HomepageSlideService;
use App\Filament\Resources\HomepageSlides\HomepageSlideResource;
use App\Models\HomepageSlide;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageHomepageSlides extends ManageRecords
{
    protected static string $resource = HomepageSlideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->after(function (array $data, HomepageSlide $record): void {
                app(SyncHomepageSlideMedia::class)->handle($record, $data);
                app(HomepageSlideService::class)->forget();
            }),
        ];
    }
}
