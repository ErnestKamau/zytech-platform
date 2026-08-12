<?php

namespace App\Filament\Resources\ArticleTags\Pages;

use App\Domains\Knowledge\Services\KnowledgeCentreService;
use App\Filament\Resources\ArticleTags\ArticleTagResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageArticleTags extends ManageRecords
{
    protected static string $resource = ArticleTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->after(fn () => app(KnowledgeCentreService::class)->forget()),
        ];
    }
}
