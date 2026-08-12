<?php

namespace App\Domains\Knowledge\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Knowledge\Events\ArticleArchived;
use App\Domains\Knowledge\Events\ArticleCreated;
use App\Domains\Knowledge\Events\ArticlePublished;
use App\Domains\Knowledge\Events\ArticleUpdated;
use App\Domains\Knowledge\Events\FeaturedArticleChanged;
use App\Domains\Knowledge\Services\KnowledgeCentreService;

final class ClearArticleCache extends BaseListener
{
    public function __construct(private readonly KnowledgeCentreService $articles) {}

    public function handle(
        ArticleCreated|ArticlePublished|ArticleUpdated|ArticleArchived|FeaturedArticleChanged $event,
    ): void {
        $this->articles->forget($event->article->slug);
    }
}
