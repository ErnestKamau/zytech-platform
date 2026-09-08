<?php

namespace App\Filament\Resources\Payments;

use App\Core\Enums\PaymentStatus;
use App\Core\Filament\BaseResource;
use App\Domains\Commerce\Services\PaymentService;
use App\Filament\Resources\Payments\Pages\ManagePayments;
use App\Models\Payment;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentResource extends BaseResource
{
    protected static ?string $model = Payment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static string|\UnitEnum|null $navigationGroup = 'Commerce';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('order_id')
                ->relationship('order', 'order_number')
                ->searchable(),
            Select::make('invoice_id')
                ->relationship('invoice', 'reference_number')
                ->searchable(),
            TextInput::make('amount')->numeric()->required(),
            TextInput::make('currency')->maxLength(3)->default('KES'),
            TextInput::make('method')->maxLength(100),
            Select::make('status')
                ->options(collect(PaymentStatus::cases())->mapWithKeys(
                    fn (PaymentStatus $status): array => [$status->value => $status->label()]
                ))
                ->required()
                ->default(PaymentStatus::Completed->value),
            TextInput::make('reference')->maxLength(255),
            DateTimePicker::make('paid_at'),
            Textarea::make('notes')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.order_number')->label('Order')->placeholder('—'),
                TextColumn::make('invoice.reference_number')->label('Invoice')->placeholder('—'),
                TextColumn::make('amount')->money(fn (Payment $record): string => $record->currency ?: 'KES'),
                TextColumn::make('method'),
                TextColumn::make('status')->badge()->formatStateUsing(
                    fn (PaymentStatus $state): string => $state->label()
                ),
                TextColumn::make('reference')->toggleable(),
                TextColumn::make('paid_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(
                    collect(PaymentStatus::cases())->mapWithKeys(
                        fn (PaymentStatus $status): array => [$status->value => $status->label()]
                    )->all()
                ),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('markCompleted')
                    ->label('Mark completed')
                    ->visible(fn (Payment $record): bool => $record->status !== PaymentStatus::Completed)
                    ->requiresConfirmation()
                    ->action(fn (Payment $record) => app(PaymentService::class)->markCompleted($record)),
                Action::make('refund')
                    ->color('danger')
                    ->visible(fn (Payment $record): bool => $record->status === PaymentStatus::Completed)
                    ->requiresConfirmation()
                    ->action(fn (Payment $record) => app(PaymentService::class)->refund($record)),
                EditAction::make()
                    ->after(fn (Payment $record) => ManagePayments::reconcile($record)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePayments::route('/'),
        ];
    }
}
