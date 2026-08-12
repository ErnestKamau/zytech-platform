<?php

namespace App\Domains\Knowledge\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Knowledge\Events\ArticleCreated;
use App\Domains\Knowledge\Events\ArticlePublished;
use App\Domains\Knowledge\Events\ArticleUpdated;
use App\Infrastructure\Queue\QueueName;
use Illuminate\Support\Facades\Log;

final class IndexArticle extends BaseListener
{
    public string $queue = QueueName::SEARCH;

    public function handle(ArticleCreated|ArticlePublished|ArticleUpdated $event): void
    {
        Log::info('article.indexed', [
            'article_id' => $event->article->getKey(),
            'slug' => $event->article->slug,
        ]);
    }
}
