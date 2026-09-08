<?php

namespace App\Filament\Resources\Carts;

use App\Core\Enums\CartStatus;
use App\Core\Filament\BaseResource;
use App\Filament\Resources\Carts\Pages\ManageCarts;
use App\Models\Cart;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CartResource extends BaseResource
{
    protected static ?string $model = Cart::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static string|\UnitEnum|null $navigationGroup = 'Commerce';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Carts';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('id')->disabled(),
            Select::make('status')
                ->options(collect(CartStatus::cases())->mapWithKeys(
                    fn (CartStatus $status): array => [$status->value => $status->label()]
                ))
                ->disabled(),
            TextInput::make('currency')->disabled(),
            TextInput::make('session_token')->disabled(),
            Textarea::make('notes')->rows(3)->columnSpanFull()->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')->label('Client')->placeholder('Guest'),
                TextColumn::make('status')->badge()->formatStateUsing(
                    fn (CartStatus $state): string => $state->label()
                ),
                TextColumn::make('items_count')->counts('items')->label('Lines'),
                TextColumn::make('currency'),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(
                    collect(CartStatus::cases())->mapWithKeys(
                        fn (CartStatus $status): array => [$status->value => $status->label()]
                    )->all()
                ),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCarts::route('/'),
        ];
    }
}
