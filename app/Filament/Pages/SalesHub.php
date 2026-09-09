<?php

namespace App\Filament\Pages;

use App\Filament\Resources\LeadSources\LeadSourceResource;
use App\Filament\Resources\QuotationRequests\QuotationRequestResource;
use App\Filament\Resources\Quotations\QuotationResource;
use App\Filament\Resources\SiteVisits\SiteVisitResource;
use App\Models\Quotation;
use App\Models\QuotationRequest;
use App\Models\SiteVisit;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class SalesHub extends AdminHubPage
{
    protected static ?string $navigationLabel = 'Sales';

    protected static ?string $title = 'Sales';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static ?int $navigationSort = 3;

    protected function getHubSubheading(): ?string
    {
        return 'RFQs, quotations, site visits, and lead sources.';
    }

    public function getHubHeaderActions(): array
    {
        return [
            Action::make('requests')
                ->label('Requests')
                ->icon(Heroicon::OutlinedInbox)
                ->url(QuotationRequestResource::getUrl()),
            Action::make('quotations')
                ->label('Quotations')
                ->icon(Heroicon::OutlinedDocumentText)
                ->url(QuotationResource::getUrl())
                ->color('gray'),
        ];
    }

    public function getHubSections(): array
    {
        return [
            [
                'title' => 'Pipeline',
                'cards' => [
                    $this->hubCard('Requests', QuotationRequestResource::class, Heroicon::OutlinedInbox, count: QuotationRequest::query()->count()),
                    $this->hubCard('Quotations', QuotationResource::class, Heroicon::OutlinedDocumentText, count: Quotation::query()->count()),
                    $this->hubCard('Site visits', SiteVisitResource::class, Heroicon::OutlinedMapPin, count: SiteVisit::query()->count()),
                    $this->hubCard('Lead sources', LeadSourceResource::class, Heroicon::OutlinedSignal),
                ],
            ],
        ];
    }
}
