<?php

namespace App\Filament\Resources\ProformaInvoices;

use App\Core\Enums\CommunicationNotificationType;
use App\Core\Enums\NotificationChannel;
use App\Core\Enums\ProformaInvoiceStatus;
use App\Core\Filament\BaseResource;
use App\Core\Filament\BusinessHistoryAction;
use App\Domains\Communication\Services\CommunicationService;
use App\Domains\Communication\Services\TwilioWhatsAppService;
use App\Domains\Communication\Support\PhoneNumber;
use App\Filament\Resources\ProformaInvoices\Pages\ManageProformaInvoices;
use App\Models\ProformaInvoice;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\URL;

class ProformaInvoiceResource extends BaseResource
{
    protected static ?string $model = ProformaInvoice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCurrencyDollar;

    protected static string|\UnitEnum|null $navigationGroup = 'Commerce';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'proforma invoice';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('reference_number')->disabled(),
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
                TextColumn::make('quotation.reference_number')->label('Quotation'),
                TextColumn::make('status')->badge()->formatStateUsing(
                    fn (ProformaInvoiceStatus $state): string => $state->label()
                ),
                TextColumn::make('total_amount')->money(fn (ProformaInvoice $record): string => $record->currency ?: 'KES'),
                TextColumn::make('issued_at')->date(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                BusinessHistoryAction::make(metaKeys: ['proforma_invoice_id']),
                Action::make('view_document')
                    ->label('View')
                    ->icon(Heroicon::OutlinedDocumentMagnifyingGlass)
                    ->color('gray')
                    ->url(fn (ProformaInvoice $record): string => route('filament.admin.documents.proforma', ['proformaInvoice' => $record]), shouldOpenInNewTab: true),
                Action::make('send_email')
                    ->label('Email')
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->requiresConfirmation()
                    ->modalDescription('Resend the proforma invoice PDF to the client by email.')
                    ->action(function (ProformaInvoice $record): void {
                        $record->loadMissing(['client', 'quotation.request']);
                        $email = $record->client?->email ?? $record->quotation?->request?->email;

                        if ($email === null || $email === '') {
                            Notification::make()->danger()->title('No email on file')->send();

                            return;
                        }

                        app(CommunicationService::class)->notify(
                            type: CommunicationNotificationType::ProformaInvoiceIssued->value,
                            recipientEmail: $email,
                            user: User::query()->where('email', $email)->first(),
                            client: $record->client,
                            templateKey: 'proforma-invoice-issued',
                            replacements: [
                                'name' => (string) ($record->client?->name ?? 'there'),
                                'reference' => (string) $record->reference_number,
                                'message' => 'Review your proforma invoice in the client portal: '.route('portal.quotations'),
                            ],
                            channels: [NotificationChannel::Mail],
                            meta: [
                                'proforma_invoice_id' => $record->id,
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
                    ->visible(fn (): bool => app(TwilioWhatsAppService::class)->configured())
                    ->requiresConfirmation()
                    ->modalDescription('Send the proforma invoice PDF to the client via WhatsApp.')
                    ->action(function (ProformaInvoice $record): void {
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
                            'documents.proforma',
                            now()->addHours(24),
                            ['proformaInvoice' => $record->id],
                        );

                        app(CommunicationService::class)->notify(
                            type: CommunicationNotificationType::ProformaInvoiceIssued->value,
                            recipientEmail: $email,
                            user: $email !== '' ? User::query()->where('email', $email)->first() : null,
                            client: $record->client,
                            replacements: ['name' => (string) $name, 'reference' => (string) $record->reference_number],
                            channels: [NotificationChannel::WhatsApp],
                            meta: [
                                'proforma_invoice_id' => $record->id,
                                'quotation_id' => $record->quotation_id,
                                'client_id' => $record->client_id,
                                'phone' => $phone,
                                'whatsapp_media_url' => $mediaUrl,
                                'whatsapp_template_vars' => ['1' => (string) $name, '2' => (string) $record->reference_number],
                            ],
                            subject: 'Proforma invoice '.$record->reference_number,
                            body: 'Your proforma invoice '.$record->reference_number.' is ready.',
                        );

                        Notification::make()->success()->title('WhatsApp message sent')->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageProformaInvoices::route('/'),
        ];
    }
}
