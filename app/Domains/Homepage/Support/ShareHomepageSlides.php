<?php

namespace App\Domains\Homepage\Support;

use App\Domains\Homepage\Services\HomepageSlideService;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

final class ShareHomepageSlides
{
    public function __construct(
        private readonly HomepageSlideService $slides,
    ) {}

    public function compose(View $view): void
    {
        if (! $this->tablesReady()) {
            $view->with('homepageSlides', collect());

            return;
        }

        try {
            $view->with('homepageSlides', $this->slides->active());
        } catch (\Throwable) {
            $view->with('homepageSlides', collect());
        }
    }

    private function tablesReady(): bool
    {
        try {
            return Schema::hasTable('homepage_slides');
        } catch (\Throwable) {
            return false;
        }
    }
}
