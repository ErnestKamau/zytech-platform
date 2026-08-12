<?php

namespace App\Domains\Knowledge\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Knowledge\Events\ArticleArchived;
use App\Domains\Knowledge\Events\ArticleCreated;
use App\Domains\Knowledge\Events\ArticlePublished;
use App\Domains\Knowledge\Events\ArticleUpdated;
use App\Domains\Knowledge\Events\FeaturedArticleChanged;
use App\Infrastructure\Queue\QueueName;
use Illuminate\Support\Facades\Log;

final class BroadcastArticleChanges extends BaseListener
{
    public string $queue = QueueName::BROADCAST;

    public function handle(
        ArticleCreated|ArticlePublished|ArticleUpdated|ArticleArchived|FeaturedArticleChanged $event,
    ): void {
        Log::info('article.broadcast', [
            'event' => class_basename($event),
            'article_id' => $event->article->getKey(),
            'slug' => $event->article->slug,
        ]);
    }
}
