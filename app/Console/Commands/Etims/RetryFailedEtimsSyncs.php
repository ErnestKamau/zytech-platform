<?php

namespace App\Console\Commands\Etims;

use App\Core\Enums\EtimsSyncStatus;
use App\Core\Enums\InvoiceStatus;
use App\Core\Enums\InvoiceType;
use App\Domains\Commerce\Listeners\SubmitInvoiceToEtims;
use App\Domains\Commerce\Services\Etims\EtimsSalesApiService;
use App\Domains\Commerce\Services\Etims\EtimsSettingsService;
use App\Models\Invoice;
use Illuminate\Console\Command;

class RetryFailedEtimsSyncs extends Command
{
    protected $signature = 'zytech:etims-retry-failed {--limit=100}';

    protected $description = 'Re-submit issued invoices/credit notes that have not yet synced to KRA eTIMS';

    public function handle(EtimsSettingsService $settingsService, EtimsSalesApiService $salesApi): int
    {
        if (! $settingsService->isEnabled()) {
            $this->line('eTIMS is not enabled; nothing to do.');

            return self::SUCCESS;
        }

        $invoices = Invoice::query()
            ->where('status', InvoiceStatus::Issued->value)
            ->where('kra_sync_status', '!=', EtimsSyncStatus::Synced->value)
            ->limit((int) $this->option('limit'))
            ->get();

        $synced = 0;
        $skipped = 0;

        foreach ($invoices as $invoice) {
            if ($invoice->type === InvoiceType::CreditNote && $invoice->parent_invoice_id === null) {
                $this->warn("Skipping credit note {$invoice->reference_number}: missing parent invoice");
                $skipped++;

                continue;
            }

            try {
                SubmitInvoiceToEtims::submit($invoice, $settingsService, $salesApi);
                $this->line("Synced {$invoice->reference_number}");
                $synced++;
            } catch (\Throwable $e) {
                $this->warn("Failed {$invoice->reference_number}: {$e->getMessage()}");
                $skipped++;
            }
        }

        $this->info("Synced: {$synced}, skipped: {$skipped}");

        return self::SUCCESS;
    }
}
