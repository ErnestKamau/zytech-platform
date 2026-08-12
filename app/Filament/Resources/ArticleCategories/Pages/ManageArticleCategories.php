<?php

namespace App\Filament\Resources\ArticleCategories\Pages;

use App\Domains\Knowledge\Services\KnowledgeCentreService;
use App\Filament\Resources\ArticleCategories\ArticleCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageArticleCategories extends ManageRecords
{
    protected static string $resource = ArticleCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->after(fn () => app(KnowledgeCentreService::class)->forget()),
        ];
    }
}
