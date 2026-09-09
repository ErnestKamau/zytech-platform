<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ArticleAuthors\ArticleAuthorResource;
use App\Filament\Resources\ArticleCategories\ArticleCategoryResource;
use App\Filament\Resources\ArticleFaqs\ArticleFaqResource;
use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\ArticleTags\ArticleTagResource;
use App\Models\Article;
use Filament\Support\Icons\Heroicon;

class KnowledgeHub extends AdminHubPage
{
    protected static ?string $navigationLabel = 'Knowledge';

    protected static ?string $title = 'Knowledge Centre';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?int $navigationSort = 9;

    protected function getHubSubheading(): ?string
    {
        return 'Articles, taxonomy, authors, and FAQs.';
    }

    public function getHubSections(): array
    {
        return [
            [
                'title' => 'Content',
                'cards' => [
                    $this->hubCard('Articles', ArticleResource::class, Heroicon::OutlinedBookOpen, count: Article::query()->count()),
                    $this->hubCard('Categories', ArticleCategoryResource::class, Heroicon::OutlinedTag),
                    $this->hubCard('Tags', ArticleTagResource::class, Heroicon::OutlinedHashtag),
                    $this->hubCard('Authors', ArticleAuthorResource::class, Heroicon::OutlinedPencilSquare),
                    $this->hubCard('Article FAQs', ArticleFaqResource::class, Heroicon::OutlinedQuestionMarkCircle),
                ],
            ],
        ];
    }
}
