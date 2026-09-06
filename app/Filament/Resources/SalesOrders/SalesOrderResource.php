<?php

namespace App\Filament\Resources\SalesOrders;

use App\Core\Enums\SalesOrderStatus;
use App\Core\Filament\BaseResource;
use App\Filament\Resources\SalesOrders\Pages\ManageSalesOrders;
use App\Models\SalesOrder;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SalesOrderResource extends BaseResource
{
    protected static ?string $model = SalesOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|\UnitEnum|null $navigationGroup = 'Commerce';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Sales orders';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('reference_number')->disabled(),
            Select::make('status')
                ->options(collect(SalesOrderStatus::cases())->mapWithKeys(
                    fn (SalesOrderStatus $status): array => [$status->value => $status->label()]
                ))
                ->required(),
            TextInput::make('total_amount')->numeric()->disabled(),
            Textarea::make('notes')->rows(3)->columnSpanFull(),
            Textarea::make('payment_terms')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_number')->searchable(),
                TextColumn::make('client.name')->label('Client'),
                TextColumn::make('quotation.reference_number')->label('Quote'),
                TextColumn::make('status')->badge()->formatStateUsing(
                    fn (SalesOrderStatus $state): string => $state->label()
                ),
                TextColumn::make('total_amount')->money(fn (SalesOrder $record): string => $record->currency ?: 'KES'),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSalesOrders::route('/'),
        ];
    }
}
