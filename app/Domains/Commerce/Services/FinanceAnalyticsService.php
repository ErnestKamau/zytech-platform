<?php

namespace App\Domains\Commerce\Services;

use App\Core\Enums\InvoiceStatus;
use App\Core\Enums\PaymentStatus;
use App\Core\Services\BaseService;
use App\Models\Invoice;
use App\Models\Payment;

final class FinanceAnalyticsService extends BaseService
{
    /**
     * @return array{
     *     revenue_mtd: float,
     *     revenue_last_30d: float,
     *     outstanding: float,
     *     invoices_paid: int,
     *     invoices_overdue: int,
     *     invoices_open: int,
     *     average_payment: float
     * }
     */
    public function snapshot(): array
    {
        $completed = PaymentStatus::Completed;
        $monthStart = now()->startOfMonth();
        $thirtyDaysAgo = now()->subDays(30);

        $revenueMtd = (float) Payment::query()
            ->where('status', $completed)
            ->where('paid_at', '>=', $monthStart)
            ->sum('amount');

        $revenueLast30d = (float) Payment::query()
            ->where('status', $completed)
            ->where('paid_at', '>=', $thirtyDaysAgo)
            ->sum('amount');

        $outstanding = (float) Invoice::query()
            ->whereNotIn('status', [InvoiceStatus::Void, InvoiceStatus::Cancelled])
            ->sum('amount_due');

        $invoicesPaid = Invoice::query()->where('status', InvoiceStatus::Paid)->count();
        $invoicesOverdue = Invoice::query()->where('status', InvoiceStatus::Overdue)->count();
        $invoicesOpen = Invoice::query()
            ->whereIn('status', [
                InvoiceStatus::Issued,
                InvoiceStatus::PartiallyPaid,
                InvoiceStatus::Overdue,
            ])
            ->count();

        $averagePayment = (float) Payment::query()
            ->where('status', $completed)
            ->avg('amount');

        return [
            'revenue_mtd' => round($revenueMtd, 2),
            'revenue_last_30d' => round($revenueLast30d, 2),
            'outstanding' => round($outstanding, 2),
            'invoices_paid' => $invoicesPaid,
            'invoices_overdue' => $invoicesOverdue,
            'invoices_open' => $invoicesOpen,
            'average_payment' => round($averagePayment, 2),
        ];
    }
}
