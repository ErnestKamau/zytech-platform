<?php

namespace App\Domains\Commerce\Services;

use App\Core\Enums\InvoiceStatus;
use App\Core\Enums\PaymentStatus;
use App\Core\Services\BaseService;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

final class FinanceAnalyticsService extends BaseService
{
    /**
     * Open AR statuses — drafts must not inflate outstanding.
     *
     * @return list<InvoiceStatus>
     */
    public static function openInvoiceStatuses(): array
    {
        return [
            InvoiceStatus::Issued,
            InvoiceStatus::PartiallyPaid,
            InvoiceStatus::Overdue,
        ];
    }

    /**
     * @return array{
     *     revenue_mtd: float,
     *     revenue_last_30d: float,
     *     outstanding: float,
     *     overdue_amount: float,
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
            ->whereIn('status', self::openInvoiceStatuses())
            ->sum('amount_due');

        $overdueAmount = (float) Invoice::query()
            ->where('status', InvoiceStatus::Overdue)
            ->sum('amount_due');

        $invoicesPaid = Invoice::query()->where('status', InvoiceStatus::Paid)->count();
        $invoicesOverdue = Invoice::query()->where('status', InvoiceStatus::Overdue)->count();
        $invoicesOpen = Invoice::query()
            ->whereIn('status', self::openInvoiceStatuses())
            ->count();

        $averagePayment = (float) Payment::query()
            ->where('status', $completed)
            ->avg('amount');

        return [
            'revenue_mtd' => round($revenueMtd, 2),
            'revenue_last_30d' => round($revenueLast30d, 2),
            'outstanding' => round($outstanding, 2),
            'overdue_amount' => round($overdueAmount, 2),
            'invoices_paid' => $invoicesPaid,
            'invoices_overdue' => $invoicesOverdue,
            'invoices_open' => $invoicesOpen,
            'average_payment' => round($averagePayment, 2),
        ];
    }

    /**
     * Daily completed-payment revenue for the last N days (inclusive of today).
     *
     * @return array{labels: list<string>, values: list<float>}
     */
    public function revenueSeries(int $days = 30): array
    {
        $days = max(1, $days);
        $start = now()->subDays($days - 1)->startOfDay();

        $rows = Payment::query()
            ->select([
                DB::raw('DATE(paid_at) as day'),
                DB::raw('SUM(amount) as total'),
            ])
            ->where('status', PaymentStatus::Completed)
            ->where('paid_at', '>=', $start)
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $labels = [];
        $values = [];

        for ($i = 0; $i < $days; $i++) {
            $day = $start->copy()->addDays($i);
            $key = $day->toDateString();
            $labels[] = $day->format('j M');
            $values[] = round((float) ($rows[$key] ?? 0), 2);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}
