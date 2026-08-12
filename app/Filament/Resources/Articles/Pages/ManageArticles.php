<?php

namespace App\Filament\Resources\Articles\Pages;

use App\Domains\Knowledge\Services\KnowledgeCentreService;
use App\Filament\Resources\Articles\ArticleResource;
use App\Models\Article;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageArticles extends ManageRecords
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->after(fn (Article $record) => app(KnowledgeCentreService::class)->persisted($record, created: true)),
        ];
    }
}
