<?php

namespace App\Filament\Resources\Invoices;

use App\Core\Enums\CommunicationNotificationType;
use App\Core\Enums\InvoiceStatus;
use App\Core\Enums\NotificationChannel;
use App\Core\Enums\PaymentStatus;
use App\Core\Filament\BaseResource;
use App\Core\Filament\BusinessHistoryAction;
use App\Domains\Commerce\Services\PaymentService;
use App\Domains\Commerce\Services\SalesOrderService;
use App\Domains\Communication\Services\CommunicationService;
use App\Domains\Communication\Services\TwilioWhatsAppService;
use App\Domains\Communication\Support\PhoneNumber;
use App\Filament\Resources\Invoices\Pages\ManageInvoices;
use App\Models\Invoice;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\URL;

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
                BusinessHistoryAction::make(metaKeys: ['invoice_id']),
                Action::make('view_document')
                    ->label('View')
                    ->icon(Heroicon::OutlinedDocumentMagnifyingGlass)
                    ->color('gray')
                    ->url(fn (Invoice $record): string => route('filament.admin.documents.invoice', ['invoice' => $record]), shouldOpenInNewTab: true),
                Action::make('issue')
                    ->visible(fn (Invoice $record): bool => $record->status === InvoiceStatus::Draft)
                    ->requiresConfirmation()
                    ->modalDescription('Issues the invoice, generates its PDF, and emails the client (plus WhatsApp if configured).')
                    ->action(function (Invoice $record): void {
                        app(SalesOrderService::class)->issueInvoice($record);

                        Notification::make()->success()->title('Invoice issued')->send();
                    }),
                Action::make('send_email')
                    ->label('Email')
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->visible(fn (Invoice $record): bool => $record->status !== InvoiceStatus::Draft)
                    ->requiresConfirmation()
                    ->modalDescription('Resend the invoice PDF to the client by email.')
                    ->action(function (Invoice $record): void {
                        $record->loadMissing(['client', 'quotation.request']);
                        $email = $record->client?->email ?? $record->quotation?->request?->email;

                        if ($email === null || $email === '') {
                            Notification::make()->danger()->title('No email on file')->send();

                            return;
                        }

                        app(CommunicationService::class)->notify(
                            type: CommunicationNotificationType::InvoiceIssued->value,
                            recipientEmail: $email,
                            user: User::query()->where('email', $email)->first(),
                            client: $record->client,
                            templateKey: 'invoice-issued',
                            replacements: [
                                'name' => (string) ($record->client?->name ?? 'there'),
                                'reference' => (string) $record->reference_number,
                                'message' => 'View, download, and pay your invoice in the client portal: '.route('portal.invoices'),
                            ],
                            channels: [NotificationChannel::Mail],
                            meta: [
                                'invoice_id' => $record->id,
                                'quotation_id' => $record->quotation_id,
                                'client_id' => $record->client_id,
                            ],
                        );

                        Notification::make()->success()->title('Email sent')->send();
                    }),
                Action::make('send_whatsapp')
                    ->label('WhatsApp')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->color('gray')
                    ->visible(fn (Invoice $record): bool => $record->status !== InvoiceStatus::Draft && app(TwilioWhatsAppService::class)->configured())
                    ->requiresConfirmation()
                    ->modalDescription('Send the invoice PDF to the client via WhatsApp.')
                    ->action(function (Invoice $record): void {
                        $record->loadMissing(['client', 'quotation.request']);
                        $phone = $record->client?->phone ?? $record->quotation?->request?->phone;
                        $e164 = PhoneNumber::toE164Kenyan($phone);

                        if ($e164 === null) {
                            Notification::make()->danger()->title('No valid WhatsApp number on file')->send();

                            return;
                        }

                        $email = $record->client?->email ?? $record->quotation?->request?->email ?? '';
                        $name = $record->client?->name ?? $record->quotation?->request?->full_name ?? 'there';

                        $mediaUrl = URL::temporarySignedRoute(
                            'documents.invoices.show',
                            now()->addHours(24),
                            ['invoice' => $record->id],
                        );

                        app(CommunicationService::class)->notify(
                            type: CommunicationNotificationType::InvoiceIssued->value,
                            recipientEmail: $email,
                            user: $email !== '' ? User::query()->where('email', $email)->first() : null,
                            client: $record->client,
                            replacements: ['name' => (string) $name, 'reference' => (string) $record->reference_number],
                            channels: [NotificationChannel::WhatsApp],
                            meta: [
                                'invoice_id' => $record->id,
                                'quotation_id' => $record->quotation_id,
                                'client_id' => $record->client_id,
                                'phone' => $phone,
                                'whatsapp_media_url' => $mediaUrl,
                                'whatsapp_template_vars' => ['1' => (string) $name, '2' => (string) $record->reference_number],
                            ],
                            subject: 'Invoice '.$record->reference_number,
                            body: 'Your invoice '.$record->reference_number.' is ready.',
                        );

                        Notification::make()->success()->title('WhatsApp message sent')->send();
                    }),
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
