<?php

namespace App\Domains\Service\Support;

use App\Domains\Service\Services\FeaturedServiceService;
use App\Domains\Service\Services\ServiceService;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

final class ShareServices
{
    public function __construct(
        private readonly ServiceService $services,
        private readonly FeaturedServiceService $featured,
    ) {}

    public function compose(View $view): void
    {
        if (! $this->tablesReady()) {
            $view->with('publishedServices', collect());
            $view->with('featuredServices', collect());
            $view->with('serviceCategories', collect());

            return;
        }

        try {
            $view->with('publishedServices', $this->services->published());
            $view->with('featuredServices', $this->featured->current());
            $view->with('serviceCategories', $this->services->categories());
        } catch (\Throwable) {
            $view->with('publishedServices', collect());
            $view->with('featuredServices', collect());
            $view->with('serviceCategories', collect());
        }
    }

    private function tablesReady(): bool
    {
        try {
            return Schema::hasTable('services') && Schema::hasTable('service_categories');
        } catch (\Throwable) {
            return false;
        }
    }
}
