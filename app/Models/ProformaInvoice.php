<?php

namespace App\Models;

use App\Core\Enums\ProformaInvoiceStatus;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ProformaInvoice extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'reference_number',
        'client_id',
        'quotation_id',
        'sales_order_id',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'valid_until',
        'payment_terms',
        'notes',
        'issued_at',
        'superseded_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProformaInvoiceStatus::class,
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'valid_until' => 'date',
            'issued_at' => 'datetime',
            'superseded_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProformaInvoiceItem::class)->orderBy('sort_order');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProformaInvoiceDocument::class)->orderByDesc('created_at');
    }

    public function domainActivities(): MorphMany
    {
        return $this->morphMany(DomainActivity::class, 'subject')->orderByDesc('created_at');
    }
}
