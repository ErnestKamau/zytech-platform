<?php

namespace App\Filament\Resources\ProductCategories;

use App\Core\Filament\BaseResource;
use App\Domains\Product\Services\ProductService;
use App\Filament\Resources\ProductCategories\Pages\ManageProductCategories;
use App\Models\ProductCategory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductCategoryResource extends BaseResource
{
    protected static ?string $model = ProductCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|\UnitEnum|null $navigationGroup = 'Commerce';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'category';

    protected static ?string $navigationLabel = 'Product categories';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(255),
                Textarea::make('description')->rows(3)->columnSpanFull(),
                Textarea::make('icon_path')->rows(2)->columnSpanFull(),
                Select::make('image_key')
                    ->options(self::mediaImageOptions())
                    ->searchable(),
                Toggle::make('is_published')->default(true),
                TextInput::make('sort_order')->numeric()->default(0),
            ]);
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('slug'),
                IconColumn::make('is_published')->boolean(),
                TextColumn::make('products_count')->counts('products')->label('Products'),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make()
                    ->after(fn () => app(ProductService::class)->forget()),
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
            'index' => ManageProductCategories::route('/'),
        ];
    }
}
