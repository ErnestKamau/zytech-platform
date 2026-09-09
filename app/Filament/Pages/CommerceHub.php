<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Brands\BrandResource;
use App\Filament\Resources\Carts\CartResource;
use App\Filament\Resources\Fulfillments\FulfillmentResource;
use App\Filament\Resources\InventoryLevels\InventoryLevelResource;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\ProductCategories\ProductCategoryResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\ProductVariants\ProductVariantResource;
use App\Filament\Resources\PurchaseOrders\PurchaseOrderResource;
use App\Filament\Resources\SalesOrders\SalesOrderResource;
use App\Filament\Resources\Units\UnitResource;
use App\Models\Cart;
use App\Models\InventoryLevel;
use App\Models\Order;
use App\Models\Product;
use App\Models\SalesOrder;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class CommerceHub extends AdminHubPage
{
    protected static ?string $navigationLabel = 'Commerce';

    protected static ?string $title = 'Commerce';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static ?int $navigationSort = 2;

    protected function getHubSubheading(): ?string
    {
        return 'Catalog, orders, carts, and inventory.';
    }

    public function getHubHeaderActions(): array
    {
        return [
            Action::make('products')
                ->label('Products')
                ->icon(Heroicon::OutlinedCube)
                ->url(ProductResource::getUrl()),
            Action::make('orders')
                ->label('Direct orders')
                ->icon(Heroicon::OutlinedClipboardDocumentList)
                ->url(OrderResource::getUrl())
                ->color('gray'),
        ];
    }

    public function getHubSections(): array
    {
        return [
            [
                'title' => 'Catalog',
                'description' => 'Products and supporting taxonomy.',
                'cards' => [
                    $this->hubCard('Categories', ProductCategoryResource::class, Heroicon::OutlinedTag),
                    $this->hubCard('Products', ProductResource::class, Heroicon::OutlinedCube, count: Product::query()->count()),
                    $this->hubCard('Brands', BrandResource::class, Heroicon::OutlinedSparkles),
                    $this->hubCard('Variants', ProductVariantResource::class, Heroicon::OutlinedSquares2x2),
                    $this->hubCard('Units', UnitResource::class, Heroicon::OutlinedScale),
                ],
            ],
            [
                'title' => 'Orders',
                'description' => 'Sales pipeline and direct checkout.',
                'cards' => [
                    $this->hubCard('Sales orders', SalesOrderResource::class, Heroicon::OutlinedDocumentText, count: SalesOrder::query()->count()),
                    $this->hubCard('Direct orders', OrderResource::class, Heroicon::OutlinedClipboardDocumentList, count: Order::query()->count()),
                    $this->hubCard('Purchase orders', PurchaseOrderResource::class, Heroicon::OutlinedDocumentArrowDown),
                    $this->hubCard('Fulfillments', FulfillmentResource::class, Heroicon::OutlinedTruck),
                    $this->hubCard('Carts', CartResource::class, Heroicon::OutlinedShoppingCart, count: Cart::query()->count()),
                ],
            ],
            [
                'title' => 'Inventory',
                'cards' => [
                    $this->hubCard('Inventory levels', InventoryLevelResource::class, Heroicon::OutlinedArchiveBox, count: InventoryLevel::query()->count()),
                ],
            ],
        ];
    }
}
