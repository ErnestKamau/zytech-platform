<?php

namespace App\Filament\Resources\ProductCategories\Pages;

use App\Domains\Product\Services\ProductService;
use App\Filament\Resources\ProductCategories\ProductCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageProductCategories extends ManageRecords
{
    protected static string $resource = ProductCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(fn () => app(ProductService::class)->forget()),
        ];
    }
}
