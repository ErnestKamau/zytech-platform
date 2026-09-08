<?php

namespace App\Filament\Resources\ProductVariants;

use App\Core\Filament\BaseResource;
use App\Filament\Resources\ProductVariants\Pages\ManageProductVariants;
use App\Models\ProductVariant;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductVariantResource extends BaseResource
{
    protected static ?string $model = ProductVariant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCubeTransparent;

    protected static string|\UnitEnum|null $navigationGroup = 'Commerce';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Product variants';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->relationship('product', 'title')
                    ->required()
                    ->searchable(),
                TextInput::make('title')->required()->maxLength(255),
                TextInput::make('sku')->maxLength(100),
                Select::make('unit_id')
                    ->relationship('unit', 'name')
                    ->searchable(),
                TextInput::make('price_amount')->numeric()->prefix('KES'),
                TextInput::make('stock_display')->numeric()->helperText('Display-only stock count'),
                Toggle::make('is_active')->default(true),
                KeyValue::make('attributes')
                    ->keyLabel('Attribute')
                    ->valueLabel('Value')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.title')->label('Product')->searchable()->sortable(),
                TextColumn::make('title')->searchable(),
                TextColumn::make('sku')->toggleable(),
                TextColumn::make('unit.name')->label('Unit'),
                TextColumn::make('price_amount')->money('KES'),
                IconColumn::make('is_active')->boolean(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageProductVariants::route('/'),
        ];
    }
}
