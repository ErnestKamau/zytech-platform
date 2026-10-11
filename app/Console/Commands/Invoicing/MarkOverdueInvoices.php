<?php

namespace App\Console\Commands\Invoicing;

use App\Core\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Console\Command;

class MarkOverdueInvoices extends Command
{
    protected $signature = 'zytech:invoices-mark-overdue';

    protected $description = 'Flag issued/partially paid invoices whose due date has passed as Overdue';

    public function handle(): int
    {
        $invoices = Invoice::query()
            ->whereDate('due_date', '<', today())
            ->whereIn('status', [InvoiceStatus::Issued->value, InvoiceStatus::PartiallyPaid->value])
            ->get();

        foreach ($invoices as $invoice) {
            $invoice->forceFill(['status' => InvoiceStatus::Overdue])->save();
            $this->line("Marked overdue: {$invoice->reference_number}");
        }

        $this->info("Marked {$invoices->count()} invoice(s) overdue.");

        return self::SUCCESS;
    }
}
