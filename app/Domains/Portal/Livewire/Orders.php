<?php

namespace App\Domains\Portal\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Portal\Exports\PortalCollectionExport;
use App\Domains\Portal\Livewire\Concerns\ResolvesPortalClient;
use App\Domains\Portal\Services\PortalService;
use App\Models\Order;
use App\Models\SalesOrder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
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
        $rows = $this->filtered($portal)->map(function (array $row) {
            $order = $row['model'];

            if ($order instanceof SalesOrder) {
                return [
                    'type' => 'Sales order',
                    'reference' => $order->reference_number,
                    'status' => $order->status->label(),
                    'total' => $order->total_amount,
                    'currency' => $order->currency,
                    'source' => $order->quotation?->reference_number,
                ];
            }

            /** @var Order $order */
            return [
                'type' => 'Direct order',
                'reference' => $order->order_number,
                'status' => $order->status->label(),
                'total' => $order->total_amount,
                'currency' => $order->currency,
                'source' => 'Cart',
            ];
        });

        return Excel::download(
            new PortalCollectionExport($rows, ['Type', 'Reference', 'Status', 'Total', 'Currency', 'Source']),
            'portal-orders.xlsx',
        );
    }

    public function render(PortalService $portal): View
    {
        return view('livewire.portal.orders', [
            'orders' => $this->filtered($portal),
        ]);
    }

    /**
     * @return Collection<int, array{kind: string, model: SalesOrder|Order}>
     */
    private function filtered(PortalService $portal): Collection
    {
        $client = $this->portalClient();

        $sales = $portal->salesOrders($client)->map(fn (SalesOrder $order) => [
            'kind' => 'sales',
            'model' => $order,
        ]);

        $direct = $portal->directOrders($client)->map(fn (Order $order) => [
            'kind' => 'direct',
            'model' => $order,
        ]);

        return $sales->concat($direct)
            ->when($this->search !== '', function (Collection $collection) {
                $needle = mb_strtolower($this->search);

                return $collection->filter(function (array $row) use ($needle) {
                    $order = $row['model'];

                    if ($order instanceof SalesOrder) {
                        return str_contains(mb_strtolower((string) $order->reference_number), $needle)
                            || str_contains(mb_strtolower((string) ($order->quotation?->reference_number ?? '')), $needle);
                    }

                    return str_contains(mb_strtolower((string) $order->order_number), $needle);
                });
            })
            ->values();
    }
}
