<?php

namespace App\Domains\Knowledge\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Knowledge\Services\KnowledgeCentreService;
use App\Models\Article;

final class ArchiveArticle extends BaseAction
{
    public function __construct(private readonly KnowledgeCentreService $articles) {}

    public function handle(mixed ...$arguments): Article
    {
        /** @var Article $article */
        $article = $arguments[0];

        return $this->articles->archive($article);
    }
}
