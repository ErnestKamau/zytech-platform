<?php

namespace App\Filament\Resources\Orders;

use App\Core\Enums\OrderPaymentStatus;
use App\Core\Enums\OrderStatus;
use App\Core\Enums\PaymentStatus;
use App\Core\Filament\BaseResource;
use App\Domains\Commerce\Services\OrderService;
use App\Domains\Commerce\Services\PaymentService;
use App\Filament\Resources\Orders\Pages\ManageOrders;
use App\Models\Order;
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

class OrderResource extends BaseResource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static string|\UnitEnum|null $navigationGroup = 'Commerce';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Direct orders';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('order_number')->disabled(),
            Select::make('status')
                ->options(collect(OrderStatus::cases())->mapWithKeys(
                    fn (OrderStatus $status): array => [$status->value => $status->label()]
                ))
                ->disabled(),
            Select::make('payment_status')
                ->options(collect(OrderPaymentStatus::cases())->mapWithKeys(
                    fn (OrderPaymentStatus $status): array => [$status->value => $status->label()]
                ))
                ->disabled(),
            TextInput::make('total_amount')->numeric()->disabled(),
            Textarea::make('notes')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')->searchable(),
                TextColumn::make('client.name')->label('Client'),
                TextColumn::make('status')->badge()->formatStateUsing(
                    fn (OrderStatus $state): string => $state->label()
                ),
                TextColumn::make('payment_status')->badge()->formatStateUsing(
                    fn (OrderPaymentStatus $state): string => $state->label()
                ),
                TextColumn::make('total_amount')->money(fn (Order $record): string => $record->currency ?: 'KES'),
                TextColumn::make('placed_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(
                    collect(OrderStatus::cases())->mapWithKeys(
                        fn (OrderStatus $status): array => [$status->value => $status->label()]
                    )->all()
                ),
            ])
            ->defaultSort('placed_at', 'desc')
            ->recordActions([
                EditAction::make(),
                Action::make('confirm')
                    ->visible(fn (Order $record): bool => $record->status === OrderStatus::Pending)
                    ->requiresConfirmation()
                    ->action(fn (Order $record) => app(OrderService::class)->confirm($record)),
                Action::make('processing')
                    ->visible(fn (Order $record): bool => $record->status === OrderStatus::Confirmed)
                    ->requiresConfirmation()
                    ->action(fn (Order $record) => app(OrderService::class)->markProcessing($record)),
                Action::make('ready')
                    ->label('Ready for fulfillment')
                    ->visible(fn (Order $record): bool => $record->status === OrderStatus::Processing)
                    ->requiresConfirmation()
                    ->action(fn (Order $record) => app(OrderService::class)->markReadyForFulfillment($record)),
                Action::make('complete')
                    ->visible(fn (Order $record): bool => in_array($record->status, [OrderStatus::Processing, OrderStatus::ReadyForFulfillment], true))
                    ->requiresConfirmation()
                    ->action(fn (Order $record) => app(OrderService::class)->complete($record)),
                Action::make('cancel')
                    ->color('danger')
                    ->visible(fn (Order $record): bool => $record->isCancellable())
                    ->requiresConfirmation()
                    ->action(fn (Order $record) => app(OrderService::class)->cancel($record)),
                Action::make('recordPayment')
                    ->label('Record payment')
                    ->icon(Heroicon::OutlinedCreditCard)
                    ->visible(fn (Order $record): bool => $record->payment_status !== OrderPaymentStatus::Paid)
                    ->schema([
                        TextInput::make('amount')->numeric()->required(),
                        TextInput::make('method')->maxLength(100),
                        TextInput::make('reference')->maxLength(255),
                    ])
                    ->action(function (Order $record, array $data): void {
                        app(PaymentService::class)->recordForOrder($record, [
                            'amount' => $data['amount'],
                            'currency' => $record->currency,
                            'method' => $data['method'] ?? null,
                            'reference' => $data['reference'] ?? null,
                            'status' => PaymentStatus::Completed,
                        ]);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageOrders::route('/'),
        ];
    }
}
