<?php

namespace App\Domains\Knowledge\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Knowledge\Services\FeaturedArticleService;
use Illuminate\Contracts\View\View;

final class FeaturedArticles extends BaseComponent
{
    public function render(): View
    {
        return view('livewire.knowledge.featured-articles', [
            'articles' => app(FeaturedArticleService::class)->current(),
        ]);
    }
}
