<?php

namespace App\Domains\Knowledge\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Knowledge\Services\KnowledgeCentreService;
use App\Models\Article;

final class FeatureArticle extends BaseAction
{
    public function __construct(private readonly KnowledgeCentreService $articles) {}

    public function handle(mixed ...$arguments): Article
    {
        /** @var Article $article */
        $article = $arguments[0];
        $featured = (bool) ($arguments[1] ?? true);

        return $this->articles->feature($article, $featured);
    }
}
