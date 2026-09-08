<?php

namespace App\Filament\Resources\Fulfillments;

use App\Core\Enums\FulfillmentMethod;
use App\Core\Enums\FulfillmentStatus;
use App\Core\Filament\BaseResource;
use App\Domains\Commerce\Services\FulfillmentService;
use App\Filament\Resources\Fulfillments\Pages\ManageFulfillments;
use App\Models\Fulfillment;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FulfillmentResource extends BaseResource
{
    protected static ?string $model = Fulfillment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|\UnitEnum|null $navigationGroup = 'Commerce';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('order_id')->relationship('order', 'order_number')->searchable(),
            Select::make('sales_order_id')->relationship('salesOrder', 'reference_number')->searchable(),
            Select::make('status')
                ->options(collect(FulfillmentStatus::cases())->mapWithKeys(
                    fn (FulfillmentStatus $status): array => [$status->value => $status->label()]
                ))
                ->disabled(),
            Select::make('method')
                ->options(collect(FulfillmentMethod::cases())->mapWithKeys(
                    fn (FulfillmentMethod $method): array => [$method->value => $method->label()]
                )),
            TextInput::make('tracking_number'),
            TextInput::make('carrier'),
            Textarea::make('notes')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.order_number')->label('Direct order')->placeholder('—'),
                TextColumn::make('salesOrder.reference_number')->label('Sales order')->placeholder('—'),
                TextColumn::make('status')->badge()->formatStateUsing(
                    fn (FulfillmentStatus $state): string => $state->label()
                ),
                TextColumn::make('tracking_number')->toggleable(),
                TextColumn::make('carrier')->toggleable(),
                TextColumn::make('shipped_at')->dateTime()->toggleable(),
                TextColumn::make('delivered_at')->dateTime()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(
                    collect(FulfillmentStatus::cases())->mapWithKeys(
                        fn (FulfillmentStatus $status): array => [$status->value => $status->label()]
                    )->all()
                ),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                EditAction::make(),
                Action::make('picking')
                    ->visible(fn (Fulfillment $record): bool => $record->status === FulfillmentStatus::Pending)
                    ->action(fn (Fulfillment $record) => app(FulfillmentService::class)->markPicking($record)),
                Action::make('ship')
                    ->visible(fn (Fulfillment $record): bool => in_array($record->status, [FulfillmentStatus::Pending, FulfillmentStatus::Picking], true))
                    ->action(fn (Fulfillment $record) => app(FulfillmentService::class)->markShipped($record)),
                Action::make('deliver')
                    ->visible(fn (Fulfillment $record): bool => $record->status === FulfillmentStatus::Shipped)
                    ->action(fn (Fulfillment $record) => app(FulfillmentService::class)->markDelivered($record)),
                Action::make('cancel')
                    ->color('danger')
                    ->visible(fn (Fulfillment $record): bool => ! $record->status->isTerminal())
                    ->requiresConfirmation()
                    ->action(fn (Fulfillment $record) => app(FulfillmentService::class)->cancel($record)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageFulfillments::route('/'),
        ];
    }
}
