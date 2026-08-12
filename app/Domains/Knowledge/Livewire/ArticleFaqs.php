<?php

namespace App\Domains\Knowledge\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Models\Article;
use Illuminate\Contracts\View\View;

final class ArticleFaqs extends BaseComponent
{
    public string $articleId;

    public function render(): View
    {
        $article = Article::query()
            ->with(['faqs' => fn ($query) => $query->where('is_published', true)->orderBy('sort_order')])
            ->find($this->articleId);

        return view('livewire.knowledge.article-faqs', [
            'faqs' => $article?->faqs ?? collect(),
        ]);
    }
}
