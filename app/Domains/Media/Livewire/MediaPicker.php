<?php

namespace App\Domains\Media\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Media\Services\MediaSearchService;
use App\Domains\Media\Services\MediaService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

final class MediaPicker extends BaseComponent
{
    public string $query = '';

    public function render(): View
    {
        $items = trim($this->query) === ''
            ? app(MediaService::class)->recent(12)
            : app(MediaSearchService::class)->search($this->query, 12);

        return view('livewire.media.media-picker', [
            'items' => $items instanceof Collection ? $items : collect(),
        ]);
    }
}
