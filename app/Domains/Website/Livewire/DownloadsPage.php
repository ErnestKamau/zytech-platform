<?php

namespace App\Domains\Website\Livewire;

use App\Core\Enums\ArticleStatus;
use App\Core\Enums\VisibilityStatus;
use App\Core\Livewire\BaseComponent;
use App\Models\ArticleDownload;
use Illuminate\Contracts\View\View;

final class DownloadsPage extends BaseComponent
{
    public function render(): View
    {
        $downloads = ArticleDownload::query()
            ->with(['article:id,title,slug,status,visibility'])
            ->whereHas('article', function ($query): void {
                $query->where('status', ArticleStatus::Published)
                    ->where('visibility', VisibilityStatus::Public);
            })
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('livewire.website.downloads-page', [
            'downloads' => $downloads,
        ]);
    }
}
