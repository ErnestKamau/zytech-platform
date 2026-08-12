<?php

namespace App\Domains\Website\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Knowledge\Services\FeaturedArticleService;
use App\Domains\Knowledge\Services\KnowledgeCentreService;
use App\Models\ArticleCategory;
use Illuminate\Contracts\View\View;

final class KnowledgePage extends BaseComponent
{
    public ?string $category = null;

    public string $search = '';

    public function mount(?string $category = null): void
    {
        $this->category = $category;
    }

    public function render(): View
    {
        $catalogue = app(KnowledgeCentreService::class);
        $selected = $this->category
            ? $catalogue->categories()->first(fn (ArticleCategory $cat): bool => $cat->slug === $this->category)
            : null;

        if ($this->category && $selected === null) {
            abort(404);
        }

        $articles = filled(trim($this->search))
            ? $catalogue->search(trim($this->search))
            : $catalogue->published($this->category);

        return view('livewire.website.knowledge-page', [
            'selectedCategory' => $selected,
            'categories' => $catalogue->categories(),
            'articles' => $articles,
            'featured' => app(FeaturedArticleService::class)->current(),
        ]);
    }
}
