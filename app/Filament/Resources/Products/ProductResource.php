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
use App\Models\Unit;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
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

    /**
     * @return array<string, string>
     */
    public static function currencyOptions(): array
    {
        return [
            'KES' => 'KES — Kenyan Shilling',
            'USD' => 'USD — US Dollar',
            'EUR' => 'EUR — Euro',
            'GBP' => 'GBP — British Pound',
            'UGX' => 'UGX — Ugandan Shilling',
            'TZS' => 'TZS — Tanzanian Shilling',
            'RWF' => 'RWF — Rwandan Franc',
            'AED' => 'AED — UAE Dirham',
            'ZAR' => 'ZAR — South African Rand',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function unitOptions(): array
    {
        return Unit::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Unit $unit): array => [
                $unit->code => $unit->name.($unit->symbol ? ' ('.$unit->symbol.')' : ''),
            ])
            ->all();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Details')
                    ->description('Core catalogue information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Select::make('product_category_id')
                            ->relationship('category', 'name')
                            ->label('Category')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('brand_id')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('sku')
                            ->label('SKU')
                            ->maxLength(100),
                        Select::make('purchase_mode')
                            ->options(collect(PurchaseMode::cases())->mapWithKeys(
                                fn (PurchaseMode $mode): array => [$mode->value => $mode->label()]
                            ))
                            ->required()
                            ->default(PurchaseMode::Quote->value),
                        Textarea::make('body')
                            ->label('Description')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Catalogue settings')
                    ->columns(2)
                    ->schema([
                        Select::make('default_unit_id')
                            ->relationship('defaultUnit', 'name')
                            ->label('Unit of measure')
                            ->helperText('Stock / catalogue unit.')
                            ->searchable()
                            ->preload(),
                        Select::make('visibility')
                            ->options(collect(VisibilityStatus::cases())->mapWithKeys(
                                fn (VisibilityStatus $status): array => [$status->value => $status->label()]
                            ))
                            ->required()
                            ->default(VisibilityStatus::Public->value),
                        TextInput::make('stock_display')
                            ->label('Stock count')
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Toggle::make('is_featured')
                            ->label('Featured'),
                        Toggle::make('taxable')
                            ->default(true),
                    ]),

                Section::make('Product image')
                    ->schema([
                        FileUpload::make('icon_path')
                            ->hiddenLabel()
                            ->image()
                            ->disk('public')
                            ->directory('products')
                            ->visibility('public')
                            ->imageEditor()
                            ->imagePreviewHeight('160'),
                    ]),

                Section::make('Specifications')
                    ->collapsed()
                    ->schema([
                        KeyValue::make('specifications')
                            ->hiddenLabel()
                            ->keyLabel('Spec')
                            ->valueLabel('Value')
                            ->reorderable()
                            ->addActionLabel('Add specification'),
                    ]),

                Section::make('Pricing')
                    ->columns(2)
                    ->schema([
                        Select::make('pricing_model')
                            ->options(collect(PricingModel::cases())->mapWithKeys(
                                fn (PricingModel $model): array => [$model->value => $model->label()]
                            ))
                            ->required()
                            ->default(PricingModel::Fixed->value),
                        Select::make('price_currency')
                            ->label('Currency')
                            ->options(self::currencyOptions())
                            ->required()
                            ->default('KES')
                            ->live()
                            ->searchable(),
                        TextInput::make('price_amount')
                            ->label('Amount')
                            ->numeric()
                            ->prefix(fn (Get $get): string => (string) ($get('price_currency') ?: 'KES')),
                        Select::make('price_unit')
                            ->label('Price unit')
                            ->options(fn (): array => self::unitOptions())
                            ->searchable()
                            ->hintIcon(
                                'heroicon-o-information-circle',
                                'Unit the price is quoted for. Example: 1,200 + Bag means KES 1,200 per bag. Can differ from stock unit of measure.',
                            ),
                        Textarea::make('pricing_notes')
                            ->label('Notes')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('SEO')
                    ->collapsed()
                    ->columns(1)
                    ->schema([
                        TextInput::make('meta_title')->maxLength(255),
                        Textarea::make('meta_description')->rows(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('sku')->toggleable(),
                TextColumn::make('category.name')->label('Category'),
                TextColumn::make('brand.name')->label('Brand')->toggleable(),
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
                    ->modalWidth(Width::FiveExtraLarge)
                    ->mutateFormDataUsing(function (array $data): array {
                        unset($data['status']);

                        return $data;
                    })
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
}
