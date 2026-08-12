<?php

namespace App\Domains\Seo\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Knowledge\Events\ArticleArchived;
use App\Domains\Knowledge\Events\ArticleCreated;
use App\Domains\Knowledge\Events\ArticlePublished;
use App\Domains\Knowledge\Events\ArticleUpdated;
use App\Domains\Project\Events\ProjectArchived;
use App\Domains\Project\Events\ProjectCreated;
use App\Domains\Project\Events\ProjectPublished;
use App\Domains\Project\Events\ProjectUpdated;
use App\Domains\Seo\Services\SitemapService;
use App\Domains\Service\Events\ServiceArchived;
use App\Domains\Service\Events\ServiceCreated;
use App\Domains\Service\Events\ServicePublished;
use App\Domains\Service\Events\ServiceUpdated;

final class ClearSitemapCache extends BaseListener
{
    public function __construct(private readonly SitemapService $sitemap) {}

    public function handle(
        ServiceCreated|ServiceUpdated|ServicePublished|ServiceArchived|ProjectCreated|ProjectUpdated|ProjectPublished|ProjectArchived|ArticleCreated|ArticleUpdated|ArticlePublished|ArticleArchived $event,
    ): void {
        $this->sitemap->forget();
    }
}
