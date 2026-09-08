<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Domains\Commerce\Services\PaymentService;
use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Payment;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePayments extends ManageRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(fn (Payment $record) => static::reconcile($record)),
        ];
    }

    public static function reconcile(Payment $record): void
    {
        $service = app(PaymentService::class);

        if ($record->order_id !== null) {
            $service->recalculateOrderPaymentStatus($record->order);
        }

        if ($record->invoice_id !== null) {
            $service->recalculateInvoiceBalance($record->invoice);
        }
    }
}
