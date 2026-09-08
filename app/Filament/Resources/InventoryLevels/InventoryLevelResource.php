<?php

namespace App\Filament\Resources\InventoryLevels;

use App\Core\Filament\BaseResource;
use App\Domains\Commerce\Services\InventoryService;
use App\Filament\Resources\InventoryLevels\Pages\ManageInventoryLevels;
use App\Models\InventoryLevel;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InventoryLevelResource extends BaseResource
{
    protected static ?string $model = InventoryLevel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static string|\UnitEnum|null $navigationGroup = 'Commerce';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'Inventory';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('product_id')
                ->relationship('product', 'title')
                ->required()
                ->searchable(),
            Select::make('product_variant_id')
                ->relationship('variant', 'title')
                ->label('Variant')
                ->searchable(),
            TextInput::make('warehouse_code'),
            TextInput::make('on_hand')->numeric()->required()->default(0),
            TextInput::make('reserved')->numeric()->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.title')->label('Product')->searchable()->sortable(),
                TextColumn::make('variant.title')->label('Variant')->placeholder('—'),
                TextColumn::make('warehouse_code')->placeholder('Default')->toggleable(),
                TextColumn::make('on_hand'),
                TextColumn::make('reserved'),
                TextColumn::make('available')->state(fn (InventoryLevel $record): float => $record->available()),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                Action::make('receive')
                    ->label('Receive stock')
                    ->icon(Heroicon::OutlinedArrowDownOnSquareStack)
                    ->schema([
                        TextInput::make('quantity')->numeric()->required(),
                        TextInput::make('reason')->maxLength(255),
                    ])
                    ->action(fn (InventoryLevel $record, array $data) => app(InventoryService::class)
                        ->receive($record, (float) $data['quantity'], $data['reason'] ?? null)),
                Action::make('adjust')
                    ->label('Adjust')
                    ->icon(Heroicon::OutlinedAdjustmentsHorizontal)
                    ->schema([
                        TextInput::make('quantity')
                            ->numeric()
                            ->required()
                            ->helperText('Use a negative number to reduce on-hand stock.'),
                        TextInput::make('reason')->maxLength(255),
                    ])
                    ->action(fn (InventoryLevel $record, array $data) => app(InventoryService::class)
                        ->adjust($record, (float) $data['quantity'], $data['reason'] ?? null)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageInventoryLevels::route('/'),
        ];
    }
}
