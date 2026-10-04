<?php

namespace App\Filament\Resources\Quotations\Pages;

use App\Domains\Quotation\Services\QuotationService;
use App\Filament\Resources\QuotationRequests\QuotationRequestResource;
use App\Filament\Resources\Quotations\QuotationResource;
use App\Models\Quotation;
use DomainException;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Js;

/**
 * @property Quotation $record
 */
class EditQuotation extends EditRecord
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_request')
                ->label('View request')
                ->icon(Heroicon::OutlinedInbox)
                ->color('gray')
                ->visible(fn (): bool => $this->record->quotation_request_id !== null)
                ->url(fn (): string => QuotationRequestResource::getUrl('view', ['record' => $this->record->quotation_request_id])),
            Action::make('preview_pdf')
                ->label('Save & preview PDF')
                ->icon(Heroicon::OutlinedDocumentMagnifyingGlass)
                ->color('gray')
                ->action(function (): void {
                    $this->save(shouldRedirect: false, shouldSendSavedNotification: false);

                    $url = QuotationResource::pdfPreviewUrl($this->record);

                    Notification::make()
                        ->success()
                        ->title('Quotation saved')
                        ->actions([
                            Action::make('open_pdf')->label('Open PDF')->url($url, shouldOpenInNewTab: true),
                        ])
                        ->send();

                    $this->js('window.open('.Js::from($url).', "_blank")');
                }),
            Action::make('send_to_client')
                ->label(fn (): string => $this->record->sent_at === null ? 'Send to client' : 'Resend to client')
                ->icon(Heroicon::OutlinedPaperAirplane)
                ->visible(fn (): bool => in_array($this->record->status, QuotationService::SENDABLE_STATUSES, true))
                ->requiresConfirmation()
                ->modalHeading('Send quotation to client?')
                ->modalDescription('Your changes are saved first. The client is emailed and can then review, accept, or request a revision in their portal.')
                ->modalSubmitActionLabel('Send')
                ->action(function (): void {
                    $this->save(shouldRedirect: false, shouldSendSavedNotification: false);

                    try {
                        app(QuotationService::class)->sendToClient($this->record);
                    } catch (DomainException $exception) {
                        Notification::make()->danger()->title('Not sent')->body($exception->getMessage())->send();

                        return;
                    }

                    Notification::make()
                        ->success()
                        ->title('Quotation sent')
                        ->body($this->record->reference_number.' is now in the client portal, ready to accept.')
                        ->send();

                    $this->redirect(QuotationResource::getUrl('edit', ['record' => $this->record]));
                }),
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        app(QuotationService::class)->recalculate($this->record);
    }
}
