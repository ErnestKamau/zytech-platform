<?php

namespace App\Filament\Resources\QuotationRequests\Pages;

use App\Filament\Resources\QuotationRequests\QuotationRequestResource;
use App\Filament\Resources\Quotations\QuotationResource;
use App\Models\QuotationRequest;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

/**
 * @property QuotationRequest $record
 */
class ViewQuotationRequest extends ViewRecord
{
    protected static string $resource = QuotationRequestResource::class;

    public function getTitle(): string
    {
        return 'Request '.$this->record->reference_number;
    }

    protected function getHeaderActions(): array
    {
        return [
            QuotationRequestResource::prepareQuotationAction(),
            Action::make('open_quotation')
                ->label('Edit quotation')
                ->icon(Heroicon::OutlinedPencilSquare)
                ->visible(fn (): bool => $this->record->quotation !== null)
                ->url(fn (): string => QuotationResource::getUrl('edit', ['record' => $this->record->quotation])),
            Action::make('preview_pdf')
                ->label('Preview PDF')
                ->icon(Heroicon::OutlinedDocumentMagnifyingGlass)
                ->color('gray')
                ->visible(fn (): bool => $this->record->quotation !== null)
                ->url(fn (): string => QuotationResource::pdfPreviewUrl($this->record->quotation), shouldOpenInNewTab: true),
            EditAction::make()->label('Edit request')->color('gray'),
            DeleteAction::make(),
        ];
    }
}
