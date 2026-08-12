<?php

namespace App\Domains\Website\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Service\Services\ServiceService;
use App\Models\ServiceCategory;
use Illuminate\Contracts\View\View;

final class ServicesPage extends BaseComponent
{
    public ?string $category = null;

    public function mount(?string $category = null): void
    {
        $this->category = $category;
    }

    public function render(): View
    {
        $services = app(ServiceService::class);
        $selected = $this->category !== null && $this->category !== ''
            ? $services->categories()->first(
                fn (ServiceCategory $category): bool => $category->slug === $this->category,
            )
            : null;

        if ($this->category && $selected === null) {
            abort(404);
        }

        return view('livewire.website.services-page', [
            'selectedCategory' => $selected,
            'categories' => $services->categories(),
            'services' => $services->published($this->category),
        ]);
    }
}
