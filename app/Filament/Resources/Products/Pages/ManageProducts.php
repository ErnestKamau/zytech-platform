<?php

namespace App\Filament\Resources\Products\Pages;

use App\Core\Enums\ProductStatus;
use App\Domains\Product\Services\ProductService;
use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Width;

class ManageProducts extends ManageRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth(Width::FiveExtraLarge)
                ->mutateFormDataUsing(function (array $data): array {
                    $data['status'] = ProductStatus::Published->value;
                    $data['published_at'] ??= now();

                    return $data;
                })
                ->after(fn (Product $record) => app(ProductService::class)->persisted($record, created: true)),
        ];
    }
}
