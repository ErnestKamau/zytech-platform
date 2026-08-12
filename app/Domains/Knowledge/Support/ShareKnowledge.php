<?php

namespace App\Domains\Knowledge\Support;

use App\Domains\Knowledge\Services\FeaturedArticleService;
use App\Domains\Knowledge\Services\KnowledgeCentreService;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

final class ShareKnowledge
{
    public function __construct(
        private readonly KnowledgeCentreService $articles,
        private readonly FeaturedArticleService $featured,
    ) {}

    public function compose(View $view): void
    {
        if (! $this->tablesReady()) {
            $view->with('publishedArticles', collect());
            $view->with('featuredArticles', collect());

            return;
        }

        try {
            $view->with('publishedArticles', $this->articles->published());
            $view->with('featuredArticles', $this->featured->current());
        } catch (\Throwable) {
            $view->with('publishedArticles', collect());
            $view->with('featuredArticles', collect());
        }
    }

    private function tablesReady(): bool
    {
        try {
            return Schema::hasTable('articles') && Schema::hasTable('article_categories');
        } catch (\Throwable) {
            return false;
        }
    }
}
