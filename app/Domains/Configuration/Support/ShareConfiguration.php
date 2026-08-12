<?php

namespace App\Domains\Configuration\Support;

use App\Core\Enums\NavigationLocation;
use App\Domains\Configuration\Data\BrandingData;
use App\Domains\Configuration\Data\SEOData;
use App\Domains\Configuration\Services\ConfigurationService;
use App\Domains\Configuration\Services\FeatureFlagService;
use App\Domains\Configuration\Services\NavigationService;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

final class ShareConfiguration
{
    public function __construct(
        private readonly ConfigurationService $configuration,
        private readonly NavigationService $navigation,
        private readonly FeatureFlagService $flags,
    ) {}

    public function compose(View $view): void
    {
        if (! $this->tablesReady()) {
            $view->with('platform', $this->fallback());

            return;
        }

        try {
            $view->with('platform', [
                'branding' => $this->configuration->branding(),
                'seo' => $this->configuration->seo(),
                'contact' => $this->configuration->contact(),
                'social' => $this->configuration->social(),
                'headerNav' => $this->navigation->published(NavigationLocation::Header),
                'footerNav' => $this->navigation->published(NavigationLocation::Footer),
                'flags' => $this->flags->all(),
            ]);
        } catch (\Throwable) {
            $view->with('platform', $this->fallback());
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function fallback(): array
    {
        return [
            'branding' => BrandingData::fromArray([]),
            'seo' => SEOData::fromArray([]),
            'contact' => [
                'email' => 'hello@zytech.co.ke',
                'phone' => '+254 700 000 000',
                'location' => 'Nairobi, Kenya',
                'service_area' => 'Serving Nairobi, Kiambu, and nationwide',
            ],
            'social' => [],
            'headerNav' => null,
            'footerNav' => null,
            'flags' => [],
        ];
    }

    private function tablesReady(): bool
    {
        try {
            return Schema::hasTable('settings');
        } catch (\Throwable) {
            return false;
        }
    }
}
