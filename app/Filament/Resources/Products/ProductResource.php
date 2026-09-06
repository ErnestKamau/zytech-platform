<?php

namespace App\Filament\Resources\Products;

use App\Core\Enums\PricingModel;
use App\Core\Enums\ProductStatus;
use App\Core\Enums\PurchaseMode;
use App\Core\Enums\VisibilityStatus;
use App\Core\Filament\BaseResource;
use App\Domains\Product\Actions\ArchiveProduct;
use App\Domains\Product\Actions\FeatureProduct;
use App\Domains\Product\Actions\PublishProduct;
use App\Domains\Product\Services\ProductService;
use App\Filament\Resources\Products\Pages\ManageProducts;
use App\Models\Product;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductResource extends BaseResource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static string|\UnitEnum|null $navigationGroup = 'Commerce';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->required()->maxLength(255),
                Select::make('product_category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable(),
                TextInput::make('sku')->maxLength(100),
                Select::make('purchase_mode')
                    ->options(collect(PurchaseMode::cases())->mapWithKeys(
                        fn (PurchaseMode $mode): array => [$mode->value => $mode->label()]
                    ))
                    ->required()
                    ->default(PurchaseMode::Quote->value),
                Select::make('status')
                    ->options(collect(ProductStatus::cases())->mapWithKeys(
                        fn (ProductStatus $status): array => [$status->value => $status->label()]
                    ))
                    ->required()
                    ->default(ProductStatus::Draft->value),
                Select::make('visibility')
                    ->options(collect(VisibilityStatus::cases())->mapWithKeys(
                        fn (VisibilityStatus $status): array => [$status->value => $status->label()]
                    ))
                    ->required()
                    ->default(VisibilityStatus::Public->value),
                Toggle::make('is_featured'),
                TextInput::make('sort_order')->numeric()->default(0),
                Textarea::make('excerpt')->rows(2)->columnSpanFull(),
                Textarea::make('body')->rows(5)->columnSpanFull(),
                TextInput::make('unit_of_measure')->helperText('e.g. bag, box, piece'),
                TextInput::make('stock_display')->numeric()->helperText('Display-only stock count'),
                Toggle::make('taxable')->default(true),
                Textarea::make('icon_path')->rows(2)->columnSpanFull(),
                Select::make('image_key')
                    ->options(self::mediaImageOptions())
                    ->searchable(),
                CheckboxList::make('gallery_keys')
                    ->options(self::mediaImageOptions())
                    ->columns(2)
                    ->columnSpanFull(),
                KeyValue::make('specifications')
                    ->keyLabel('Spec')
                    ->valueLabel('Value')
                    ->columnSpanFull(),
                Select::make('pricing_model')
                    ->options(collect(PricingModel::cases())->mapWithKeys(
                        fn (PricingModel $model): array => [$model->value => $model->label()]
                    ))
                    ->required()
                    ->default(PricingModel::Fixed->value),
                TextInput::make('price_amount')->numeric()->prefix('KES'),
                TextInput::make('price_currency')->maxLength(3)->default('KES'),
                TextInput::make('price_unit'),
                Textarea::make('pricing_notes')->rows(2)->columnSpanFull(),
                TextInput::make('meta_title')->maxLength(255)->columnSpanFull(),
                Textarea::make('meta_description')->rows(2)->columnSpanFull(),
                Select::make('og_image_key')->options(self::mediaImageOptions())->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('sku')->toggleable(),
                TextColumn::make('category.name')->label('Category'),
                TextColumn::make('purchase_mode')
                    ->badge()
                    ->formatStateUsing(fn (PurchaseMode $state): string => $state->label()),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (ProductStatus $state): string => $state->label()),
                IconColumn::make('is_featured')->boolean(),
                TextColumn::make('price_amount')->money(fn (Product $record): string => $record->price_currency ?: 'KES'),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                Action::make('publish')
                    ->visible(fn (Product $record): bool => $record->status !== ProductStatus::Published)
                    ->requiresConfirmation()
                    ->action(fn (Product $record) => app(PublishProduct::class)->handle($record)),
                Action::make('feature')
                    ->visible(fn (Product $record): bool => ! $record->is_featured && $record->isPublished())
                    ->action(fn (Product $record) => app(FeatureProduct::class)->handle($record, true)),
                Action::make('unfeature')
                    ->visible(fn (Product $record): bool => $record->is_featured)
                    ->action(fn (Product $record) => app(FeatureProduct::class)->handle($record, false)),
                Action::make('archive')
                    ->visible(fn (Product $record): bool => $record->status !== ProductStatus::Archived)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Product $record) => app(ArchiveProduct::class)->handle($record)),
                EditAction::make()
                    ->after(fn (Product $record) => app(ProductService::class)->persisted($record)),
                DeleteAction::make()
                    ->after(fn () => app(ProductService::class)->forget()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(fn () => app(ProductService::class)->forget()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageProducts::route('/'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function mediaImageOptions(): array
    {
        return collect(config('zyntech-media.images', []))
            ->mapWithKeys(fn (array $image, string $key): array => [
                $key => ($image['alt'] ?? $key).' ('.$key.')',
            ])
            ->all();
    }
}
