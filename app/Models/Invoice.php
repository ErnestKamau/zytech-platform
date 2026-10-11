<?php

namespace App\Models;

use App\Core\Enums\EtimsSyncStatus;
use App\Core\Enums\InvoiceStatus;
use App\Core\Enums\InvoiceType;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Invoice extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'reference_number',
        'client_id',
        'sales_order_id',
        'quotation_id',
        'purchase_order_id',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'amount_paid',
        'amount_due',
        'currency',
        'due_date',
        'payment_terms',
        'notes',
        'issued_at',
        'type',
        'parent_invoice_id',
        'kra_request_payload',
        'kra_response_payload',
        'kra_rcpt_no',
        'kra_sdc_id',
        'kra_invc_no',
        'kra_sync_status',
        'kra_last_error',
        'kra_synced_at',
        'voided_at',
        'void_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => InvoiceStatus::class,
            'type' => InvoiceType::class,
            'kra_sync_status' => EtimsSyncStatus::class,
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'amount_due' => 'decimal:2',
            'due_date' => 'date',
            'issued_at' => 'datetime',
            'kra_synced_at' => 'datetime',
            'voided_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderByDesc('created_at');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(InvoiceDocument::class)->orderByDesc('created_at');
    }

    public function domainActivities(): MorphMany
    {
        return $this->morphMany(DomainActivity::class, 'subject')->orderByDesc('created_at');
    }

    public function parentInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'parent_invoice_id');
    }

    public function creditNotes(): HasMany
    {
        return $this->hasMany(Invoice::class, 'parent_invoice_id');
    }

    public function isVoidable(): bool
    {
        return in_array($this->status, [InvoiceStatus::Draft, InvoiceStatus::Issued], true)
            && (float) $this->amount_paid <= 0;
    }
}
