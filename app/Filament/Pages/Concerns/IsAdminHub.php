<?php

namespace App\Filament\Pages\Concerns;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

trait IsAdminHub
{
    /**
     * @return list<array{
     *     title: string,
     *     description?: string|null,
     *     cards: list<array{
     *         label: string,
     *         description?: string|null,
     *         icon: string|BackedEnum,
     *         url: string,
     *         count?: int|string|null
     *     }>
     * }>
     */
    abstract public function getHubSections(): array;

    /**
     * @return list<Action>
     */
    public function getHubHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return $this->getHubHeaderActions();
    }

    public function getHeading(): string|Htmlable
    {
        return static::$title ?? static::getNavigationLabel();
    }

    public function getSubheading(): string|Htmlable|null
    {
        return $this->getHubSubheading();
    }

    protected function getHubSubheading(): ?string
    {
        return null;
    }

    /**
     * @param  class-string<\Filament\Resources\Resource>  $resource
     * @return array{label: string, description?: string|null, icon: string|BackedEnum, url: string, count?: int|string|null}
     */
    protected function hubCard(
        string $label,
        string $resource,
        string|BackedEnum $icon = Heroicon::OutlinedRectangleStack,
        ?string $description = null,
        int|string|null $count = null,
    ): array {
        return [
            'label' => $label,
            'description' => $description,
            'icon' => $icon,
            'url' => $resource::getUrl(),
            'count' => $count,
        ];
    }
}
