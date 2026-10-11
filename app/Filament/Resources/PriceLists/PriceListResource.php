<?php

namespace App\Filament\Resources\PriceLists;

use App\Core\Filament\BaseResource;
use App\Filament\Resources\PriceLists\Pages\ManagePriceLists;
use App\Filament\Resources\Products\ProductResource;
use App\Models\PriceList;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PriceListResource extends BaseResource
{
    protected static ?string $model = PriceList::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|\UnitEnum|null $navigationGroup = 'Commerce';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Pricelists';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('code')->required()->maxLength(50),
                Select::make('currency')->options(ProductResource::currencyOptions())->default('KES')->required(),
                DatePicker::make('valid_from'),
                DatePicker::make('valid_until'),
                Toggle::make('is_default')
                    ->label('Default')
                    ->helperText('Used when a client has no pricelist of their own.'),
                Toggle::make('active')->default(true),
                Textarea::make('notes')->rows(2)->columnSpanFull(),

                Section::make('Assigned clients')
                    ->schema([
                        Select::make('clients')
                            ->relationship('clients', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->hiddenLabel(),
                    ]),

                Section::make('Prices')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->hiddenLabel()
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->relationship('product', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('price')->numeric()->minValue(0)->required(),
                                TextInput::make('min_quantity')
                                    ->label('Min. quantity')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(1)
                                    ->helperText('For quantity-break tiers; leave at 1 for a flat price.'),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->addActionLabel('Add price'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('code')->searchable(),
                TextColumn::make('currency'),
                IconColumn::make('is_default')->boolean(),
                IconColumn::make('active')->boolean(),
                TextColumn::make('items_count')->counts('items')->label('Prices'),
                TextColumn::make('clients_count')->counts('clients')->label('Clients'),
            ])
            ->defaultSort('name')
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
            'index' => ManagePriceLists::route('/'),
        ];
    }
}
