<?php

namespace App\Domains\Knowledge\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Knowledge\Events\ArticleCreated;
use App\Domains\Knowledge\Events\ArticleUpdated;
use App\Domains\Knowledge\Services\ArticleSEOService;
use App\Infrastructure\Queue\QueueName;

final class GenerateArticleSeo extends BaseListener
{
    public string $queue = QueueName::DEFAULT;

    public function __construct(private readonly ArticleSEOService $seo) {}

    public function handle(ArticleCreated|ArticleUpdated $event): void
    {
        $this->seo->ensure($event->article);
    }
}
