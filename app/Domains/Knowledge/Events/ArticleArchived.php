<?php

namespace App\Domains\Knowledge\Events;

use App\Core\Events\BusinessEvent;
use App\Models\Article;

final class ArticleArchived extends BusinessEvent
{
    public function __construct(public Article $article) {}
}
