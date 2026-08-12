<?php

namespace App\Filament\Resources\ArticleAuthors\Pages;

use App\Domains\Knowledge\Services\KnowledgeCentreService;
use App\Filament\Resources\ArticleAuthors\ArticleAuthorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageArticleAuthors extends ManageRecords
{
    protected static string $resource = ArticleAuthorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->after(fn () => app(KnowledgeCentreService::class)->forget()),
        ];
    }
}
