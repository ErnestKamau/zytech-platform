<?php

namespace App\Filament\Resources\ArticleFaqs\Pages;

use App\Domains\Knowledge\Services\KnowledgeCentreService;
use App\Filament\Resources\ArticleFaqs\ArticleFaqResource;
use App\Models\ArticleFaq;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageArticleFaqs extends ManageRecords
{
    protected static string $resource = ArticleFaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->after(fn (ArticleFaq $record) => app(KnowledgeCentreService::class)->forget($record->article?->slug)),
        ];
    }
}
