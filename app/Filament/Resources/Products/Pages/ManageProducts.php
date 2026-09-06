<?php

namespace App\Filament\Resources\Products\Pages;

use App\Domains\Product\Services\ProductService;
use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageProducts extends ManageRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(fn (Product $record) => app(ProductService::class)->persisted($record, created: true)),
        ];
    }
}
