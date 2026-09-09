<?php

namespace App\Filament\Pages;

use App\Domains\Commerce\Services\FinanceAnalyticsService;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Invoice;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class FinanceHub extends AdminHubPage
{
    protected static ?string $navigationLabel = 'Finance';

    protected static ?string $title = 'Finance';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?int $navigationSort = 4;

    protected function getHubSubheading(): ?string
    {
        return 'Invoices, payments, and revenue snapshot.';
    }

    public function getHubHeaderActions(): array
    {
        return [
            Action::make('invoices')
                ->label('Invoices')
                ->icon(Heroicon::OutlinedDocumentCurrencyDollar)
                ->url(InvoiceResource::getUrl()),
            Action::make('payments')
                ->label('Payments')
                ->icon(Heroicon::OutlinedCreditCard)
                ->url(PaymentResource::getUrl())
                ->color('gray'),
        ];
    }

    public function getHubSections(): array
    {
        $snapshot = app(FinanceAnalyticsService::class)->snapshot();

        return [
            [
                'title' => 'Overview',
                'description' => 'KES '.number_format($snapshot['revenue_mtd'], 2).' MTD · '
                    .number_format($snapshot['outstanding'], 2).' outstanding · '
                    .$snapshot['invoices_overdue'].' overdue',
                'cards' => [
                    $this->hubCard('Invoices', InvoiceResource::class, Heroicon::OutlinedDocumentCurrencyDollar, count: Invoice::query()->count()),
                    $this->hubCard('Payments', PaymentResource::class, Heroicon::OutlinedCreditCard, count: Payment::query()->count()),
                ],
            ],
        ];
    }
}
