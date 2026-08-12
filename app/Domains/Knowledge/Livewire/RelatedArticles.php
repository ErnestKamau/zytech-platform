<?php

namespace App\Domains\Knowledge\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Knowledge\Services\KnowledgeCentreService;
use App\Models\Article;
use Illuminate\Contracts\View\View;

final class RelatedArticles extends BaseComponent
{
    public string $articleId;

    public function render(): View
    {
        $article = Article::query()->find($this->articleId);

        return view('livewire.knowledge.related-articles', [
            'articles' => $article === null
                ? collect()
                : app(KnowledgeCentreService::class)->related($article),
        ]);
    }
}
