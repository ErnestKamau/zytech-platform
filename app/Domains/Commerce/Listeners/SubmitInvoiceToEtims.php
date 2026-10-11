<?php

namespace App\Domains\Commerce\Listeners;

use App\Core\Enums\EtimsSyncStatus;
use App\Core\Enums\InvoiceType;
use App\Core\Listeners\BaseListener;
use App\Domains\Commerce\Events\InvoiceIssued;
use App\Domains\Commerce\Services\Etims\EtimsSalesApiService;
use App\Domains\Commerce\Services\Etims\EtimsSettingsService;
use App\Infrastructure\Queue\QueueName;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

final class SubmitInvoiceToEtims extends BaseListener
{
    public string $queue = QueueName::DEFAULT;

    public int $tries = 5;

    /** @var array<int, int> */
    public array $backoff = [60, 300, 900, 3600];

    public function __construct(
        private readonly EtimsSettingsService $settingsService,
        private readonly EtimsSalesApiService $salesApi,
    ) {}

    public function handle(InvoiceIssued $event): void
    {
        $this->submit($event->invoice, $this->settingsService, $this->salesApi);
    }

    /**
     * Shared by the listener and the manual retry command/action, so the
     * "should this invoice be (re)submitted" logic lives in exactly one place.
     */
    public static function submit(Invoice $invoice, EtimsSettingsService $settingsService, EtimsSalesApiService $salesApi): void
    {
        if (! $settingsService->isEnabled()) {
            Log::info('etims.sync.skipped_disabled', ['invoice_id' => $invoice->getKey()]);

            return;
        }

        if ($invoice->kra_sync_status === EtimsSyncStatus::Synced) {
            Log::info('etims.sync.skipped_already_synced', ['invoice_id' => $invoice->getKey()]);

            return;
        }

        $settings = $settingsService->settings();

        if ($invoice->type === InvoiceType::CreditNote) {
            $parent = $invoice->parentInvoice;

            if ($parent === null) {
                Log::error('etims.sync.credit_note_missing_parent', ['invoice_id' => $invoice->getKey()]);

                return;
            }

            $salesApi->sendCreditNote($invoice, $parent, $settings);

            return;
        }

        $salesApi->sendInvoice($invoice, $settings);
    }
}
