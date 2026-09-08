<?php

namespace App\Filament\Resources\Invoices;

use App\Core\Enums\InvoiceStatus;
use App\Core\Enums\PaymentStatus;
use App\Core\Filament\BaseResource;
use App\Domains\Commerce\Services\PaymentService;
use App\Domains\Commerce\Services\SalesOrderService;
use App\Filament\Resources\Invoices\Pages\ManageInvoices;
use App\Models\Invoice;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoiceResource extends BaseResource
{
    protected static ?string $model = Invoice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|\UnitEnum|null $navigationGroup = 'Commerce';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('reference_number')->disabled(),
            Select::make('status')
                ->options(collect(InvoiceStatus::cases())->mapWithKeys(
                    fn (InvoiceStatus $status): array => [$status->value => $status->label()]
                ))
                ->required(),
            TextInput::make('total_amount')->numeric()->disabled(),
            TextInput::make('amount_due')->numeric()->disabled(),
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
                TextColumn::make('salesOrder.reference_number')->label('Order'),
                TextColumn::make('status')->badge()->formatStateUsing(
                    fn (InvoiceStatus $state): string => $state->label()
                ),
                TextColumn::make('total_amount')->money(fn (Invoice $record): string => $record->currency ?: 'KES'),
                TextColumn::make('amount_paid')->money(fn (Invoice $record): string => $record->currency ?: 'KES'),
                TextColumn::make('amount_due')->money(fn (Invoice $record): string => $record->currency ?: 'KES'),
                TextColumn::make('payments_count')->counts('payments')->label('Payments'),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                Action::make('issue')
                    ->visible(fn (Invoice $record): bool => $record->status === InvoiceStatus::Draft)
                    ->requiresConfirmation()
                    ->action(fn (Invoice $record) => app(SalesOrderService::class)->issueInvoice($record)),
                Action::make('recordPayment')
                    ->label('Record payment')
                    ->icon(Heroicon::OutlinedCreditCard)
                    ->visible(fn (Invoice $record): bool => $record->status !== InvoiceStatus::Paid)
                    ->schema([
                        TextInput::make('amount')->numeric()->required(),
                        TextInput::make('method')->maxLength(100),
                        TextInput::make('reference')->maxLength(255),
                    ])
                    ->action(function (Invoice $record, array $data): void {
                        app(PaymentService::class)->recordForInvoice($record, [
                            'amount' => $data['amount'],
                            'currency' => $record->currency,
                            'method' => $data['method'] ?? null,
                            'reference' => $data['reference'] ?? null,
                            'status' => PaymentStatus::Completed,
                        ]);
                    }),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageInvoices::route('/'),
        ];
    }
}
