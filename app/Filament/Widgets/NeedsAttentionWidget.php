<?php

namespace App\Filament\Widgets;

use App\Domains\Operations\Services\AttentionItemsService;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\QuotationRequests\QuotationRequestResource;
use App\Filament\Resources\Quotations\QuotationResource;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class NeedsAttentionWidget extends Widget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.needs-attention';

    public static function canView(): bool
    {
        $user = Auth::user();

        if ($user === null) {
            return false;
        }

        return $user->can('commerce.view')
            || $user->can('commerce.manage')
            || $user->can('quotations.view')
            || $user->can('quotations.manage');
    }

    /**
     * @return list<array{
     *     key: string,
     *     severity: string,
     *     title: string,
     *     count: int,
     *     meta: string,
     *     url: string
     * }>
     */
    public function getCategories(): array
    {
        $user = Auth::user();
        $categories = app(AttentionItemsService::class)->categories();

        $urls = [
            'overdue_invoices' => InvoiceResource::getUrl('index'),
            'rfqs_awaiting_action' => QuotationRequestResource::getUrl('index'),
            'quotes_awaiting_response' => QuotationResource::getUrl('index'),
            'accepted_not_invoiced' => QuotationResource::getUrl('index'),
            'orders_awaiting_fulfilment' => OrderResource::getUrl('index'),
            'unassigned_rfqs' => QuotationRequestResource::getUrl('index'),
        ];

        $commerceKeys = ['overdue_invoices', 'orders_awaiting_fulfilment'];
        $salesKeys = ['rfqs_awaiting_action', 'quotes_awaiting_response', 'accepted_not_invoiced', 'unassigned_rfqs'];

        $canCommerce = $user?->can('commerce.view') || $user?->can('commerce.manage');
        $canSales = $user?->can('quotations.view') || $user?->can('quotations.manage');

        return collect($categories)
            ->filter(function (array $item) use ($commerceKeys, $salesKeys, $canCommerce, $canSales): bool {
                if (in_array($item['key'], $commerceKeys, true)) {
                    return (bool) $canCommerce;
                }

                if (in_array($item['key'], $salesKeys, true)) {
                    return (bool) $canSales;
                }

                return true;
            })
            ->map(fn (array $item): array => [
                ...$item,
                'url' => $urls[$item['key']] ?? '#',
            ])
            ->values()
            ->all();
    }
}
