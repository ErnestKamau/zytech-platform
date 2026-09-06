<?php

namespace App\Models;

use App\Core\Enums\PurchaseOrderStatus;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'reference_number',
        'po_number',
        'client_id',
        'quotation_id',
        'status',
        'currency',
        'total_amount',
        'document_path',
        'document_original_name',
        'notes',
        'validation_notes',
        'uploaded_by',
        'reviewed_by',
        'uploaded_at',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => PurchaseOrderStatus::class,
            'total_amount' => 'decimal:2',
            'uploaded_at' => 'datetime',
            'reviewed_at' => 'datetime',
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

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class);
    }
}
