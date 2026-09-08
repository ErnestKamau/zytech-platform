<?php

namespace App\Domains\Commerce\Services;

use App\Core\Enums\InvoiceStatus;
use App\Core\Enums\OrderPaymentStatus;
use App\Core\Enums\PaymentStatus;
use App\Core\Services\BaseService;
use App\Domains\Commerce\Exceptions\PaymentException;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;

final class PaymentService extends BaseService
{
    /**
     * @param  array{
     *     invoice_id?: ?string,
     *     order_id?: ?string,
     *     amount: float|string,
     *     currency?: string,
     *     method?: ?string,
     *     status?: PaymentStatus,
     *     reference?: ?string,
     *     paid_at?: ?\DateTimeInterface,
     *     notes?: ?string,
     * }  $data
     */
    public function recordPayment(array $data): Payment
    {
        if (empty($data['invoice_id']) && empty($data['order_id'])) {
            throw PaymentException::missingTarget();
        }

        if ((float) ($data['amount'] ?? 0) <= 0) {
            throw PaymentException::invalidAmount();
        }

        return $this->transaction(function () use ($data): Payment {
            $status = $data['status'] ?? PaymentStatus::Completed;

            $payment = Payment::query()->create([
                'invoice_id' => $data['invoice_id'] ?? null,
                'order_id' => $data['order_id'] ?? null,
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'KES',
                'method' => $data['method'] ?? null,
                'status' => $status,
                'reference' => $data['reference'] ?? null,
                'paid_at' => $data['paid_at'] ?? ($status === PaymentStatus::Completed ? now() : null),
                'notes' => $data['notes'] ?? null,
            ]);

            if ($payment->order_id !== null) {
                $this->recalculateOrderPaymentStatus($payment->order);
            }

            if ($payment->invoice_id !== null) {
                $this->recalculateInvoiceBalance($payment->invoice);
            }

            return $payment->refresh();
        });
    }

    public function recordForOrder(Order $order, array $data): Payment
    {
        return $this->recordPayment([...$data, 'order_id' => $order->id]);
    }

    public function recordForInvoice(Invoice $invoice, array $data): Payment
    {
        return $this->recordPayment([...$data, 'invoice_id' => $invoice->id]);
    }

    public function markCompleted(Payment $payment): Payment
    {
        $payment->forceFill([
            'status' => PaymentStatus::Completed,
            'paid_at' => $payment->paid_at ?? now(),
        ])->save();

        if ($payment->order_id !== null) {
            $this->recalculateOrderPaymentStatus($payment->order);
        }

        if ($payment->invoice_id !== null) {
            $this->recalculateInvoiceBalance($payment->invoice);
        }

        return $payment->refresh();
    }

    public function refund(Payment $payment): Payment
    {
        $payment->forceFill(['status' => PaymentStatus::Refunded])->save();

        if ($payment->order_id !== null) {
            $this->recalculateOrderPaymentStatus($payment->order);
        }

        if ($payment->invoice_id !== null) {
            $this->recalculateInvoiceBalance($payment->invoice);
        }

        return $payment->refresh();
    }

    /**
     * Recompute Order.payment_status from the sum of completed payments
     * against it, relative to its total_amount.
     */
    public function recalculateOrderPaymentStatus(Order $order): Order
    {
        $paid = (float) $order->payments()
            ->where('status', PaymentStatus::Completed)
            ->sum('amount');

        $total = (float) $order->total_amount;

        $status = match (true) {
            $paid <= 0 => OrderPaymentStatus::Unpaid,
            $paid < $total => OrderPaymentStatus::PartiallyPaid,
            default => OrderPaymentStatus::Paid,
        };

        $order->forceFill(['payment_status' => $status])->save();

        return $order->refresh();
    }

    /**
     * Recompute Invoice.amount_paid / amount_due / status from the sum of
     * completed payments against it.
     */
    public function recalculateInvoiceBalance(Invoice $invoice): Invoice
    {
        $paid = (float) $invoice->payments()
            ->where('status', PaymentStatus::Completed)
            ->sum('amount');

        $total = (float) $invoice->total_amount;
        $due = max($total - $paid, 0);

        $status = match (true) {
            $paid <= 0 => $invoice->status,
            $paid < $total => InvoiceStatus::PartiallyPaid,
            default => InvoiceStatus::Paid,
        };

        $invoice->forceFill([
            'amount_paid' => $paid,
            'amount_due' => $due,
            'status' => $status,
        ])->save();

        return $invoice->refresh();
    }
}
