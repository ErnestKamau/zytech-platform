<?php

namespace App\Domains\Portal\Livewire;

use App\Core\Enums\QuotationStatus;
use App\Core\Livewire\BaseComponent;
use App\Domains\Portal\Exports\PortalCollectionExport;
use App\Domains\Portal\Livewire\Concerns\ResolvesPortalClient;
use App\Domains\Portal\Services\PortalService;
use App\Models\Quotation;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Layout('layouts.portal')]
#[Title('Quotations')]
final class Quotations extends BaseComponent
{
    use ResolvesPortalClient;

    private const STATUS_TABS = [
        'all' => ['label' => 'All', 'variant' => 'neutral'],
        'in_progress' => ['label' => 'In progress', 'variant' => 'neutral', 'statuses' => [
            QuotationStatus::Draft, QuotationStatus::Pending, QuotationStatus::Reviewing, QuotationStatus::Preparing,
        ]],
        'awaiting_review' => ['label' => 'Awaiting your review', 'variant' => 'info', 'statuses' => [
            QuotationStatus::Sent, QuotationStatus::Viewed,
        ]],
        'revision_requested' => ['label' => 'Revision requested', 'variant' => 'warning', 'statuses' => [
            QuotationStatus::RevisionRequested,
        ]],
        'accepted' => ['label' => 'Accepted', 'variant' => 'success', 'statuses' => [
            QuotationStatus::Accepted, QuotationStatus::Completed,
        ]],
        'closed' => ['label' => 'Closed', 'variant' => 'danger', 'statuses' => [
            QuotationStatus::Rejected, QuotationStatus::Expired,
        ]],
    ];

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusTab = 'all';

    public function export(PortalService $portal): BinaryFileResponse
    {
        $rows = $this->filtered($portal)->map(fn (Quotation $quotation) => [
            'reference' => $quotation->reference_number,
            'title' => $quotation->title,
            'status' => $quotation->status->label(),
            'total' => $quotation->total_amount,
            'valid_until' => optional($quotation->valid_until)->toDateString(),
        ]);

        return Excel::download(
            new PortalCollectionExport($rows, ['Reference', 'Title', 'Status', 'Total', 'Valid until']),
            'portal-quotations.xlsx',
        );
    }

    public function render(PortalService $portal): View
    {
        $searched = $this->searched($portal);

        $statusTabs = collect(self::STATUS_TABS)->mapWithKeys(fn (array $tab, string $key) => [
            $key => [
                'label' => $tab['label'],
                'variant' => $tab['variant'],
                'count' => isset($tab['statuses'])
                    ? $searched->filter(fn (Quotation $quotation) => in_array($quotation->status, $tab['statuses'], true))->count()
                    : $searched->count(),
            ],
        ])->all();

        return view('livewire.portal.quotations', [
            'quotations' => $this->filtered($portal),
            'statusTabs' => $statusTabs,
        ]);
    }

    private function searched(PortalService $portal): Collection
    {
        return $portal->quotations($this->portalClient())
            ->when($this->search !== '', function ($collection) {
                $needle = mb_strtolower($this->search);

                return $collection->filter(function (Quotation $quotation) use ($needle) {
                    return str_contains(mb_strtolower((string) $quotation->reference_number), $needle)
                        || str_contains(mb_strtolower((string) $quotation->title), $needle);
                });
            })
            ->values();
    }

    private function filtered(PortalService $portal): Collection
    {
        $statuses = self::STATUS_TABS[$this->statusTab]['statuses'] ?? null;

        return $this->searched($portal)
            ->when($statuses !== null, fn ($collection) => $collection->filter(
                fn (Quotation $quotation) => in_array($quotation->status, $statuses, true)
            ))
            ->values();
    }
}
