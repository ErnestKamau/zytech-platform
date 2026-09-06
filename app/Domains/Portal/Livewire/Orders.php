<?php

namespace App\Domains\Portal\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Portal\Exports\PortalCollectionExport;
use App\Domains\Portal\Livewire\Concerns\ResolvesPortalClient;
use App\Domains\Portal\Services\PortalService;
use App\Models\SalesOrder;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Layout('layouts.portal')]
#[Title('Orders')]
final class Orders extends BaseComponent
{
    use ResolvesPortalClient;

    #[Url]
    public string $search = '';

    public function export(PortalService $portal): BinaryFileResponse
    {
        $rows = $this->filtered($portal)->map(fn (SalesOrder $order) => [
            'reference' => $order->reference_number,
            'status' => $order->status->label(),
            'total' => $order->total_amount,
            'currency' => $order->currency,
            'quote' => $order->quotation?->reference_number,
        ]);

        return Excel::download(
            new PortalCollectionExport($rows, ['Reference', 'Status', 'Total', 'Currency', 'Quote']),
            'portal-orders.xlsx',
        );
    }

    public function render(PortalService $portal): View
    {
        return view('livewire.portal.orders', [
            'orders' => $this->filtered($portal),
        ]);
    }

    private function filtered(PortalService $portal)
    {
        return $portal->salesOrders($this->portalClient())
            ->when($this->search !== '', function ($collection) {
                $needle = mb_strtolower($this->search);

                return $collection->filter(function (SalesOrder $order) use ($needle) {
                    return str_contains(mb_strtolower((string) $order->reference_number), $needle)
                        || str_contains(mb_strtolower((string) ($order->quotation?->reference_number ?? '')), $needle);
                });
            })
            ->values();
    }
}
