<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ServiceCategories\ServiceCategoryResource;
use App\Filament\Resources\ServiceFaqs\ServiceFaqResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Models\Service;
use Filament\Support\Icons\Heroicon;

class ServicesHub extends AdminHubPage
{
    protected static ?string $navigationLabel = 'Services';

    protected static ?string $title = 'Services';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?int $navigationSort = 7;

    protected function getHubSubheading(): ?string
    {
        return 'Service catalog, categories, and FAQs.';
    }

    public function getHubSections(): array
    {
        return [
            [
                'title' => 'Catalog',
                'cards' => [
                    $this->hubCard('Services', ServiceResource::class, Heroicon::OutlinedWrenchScrewdriver, count: Service::query()->count()),
                    $this->hubCard('Categories', ServiceCategoryResource::class, Heroicon::OutlinedTag),
                    $this->hubCard('Service FAQs', ServiceFaqResource::class, Heroicon::OutlinedQuestionMarkCircle),
                ],
            ],
        ];
    }
}
