<?php

namespace App\Domains\Search\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Search\Services\SearchService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;

final class SearchPage extends BaseComponent
{
    #[Url(as: 'q')]
    public string $q = '';

    public function render(SearchService $search): View
    {
        $results = $search->search($this->q, 'website', Auth::user());

        return view('livewire.website.search-page', [
            'results' => $results,
            'suggestions' => $search->suggestions($this->q),
            'popular' => $search->popular(),
        ]);
    }
}
