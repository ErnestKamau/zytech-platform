<?php

namespace App\Models;

use App\Core\Enums\QuotationStatus;
use App\Core\Enums\QuotationType;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'reference_number',
        'quotation_request_id',
        'sales_lead_id',
        'title',
        'type',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'valid_until',
        'revision_number',
        'notes',
        'terms',
        'prepared_by',
        'approved_by',
        'sent_at',
        'accepted_at',
        'rejected_at',
        'converted_project_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => QuotationType::class,
            'status' => QuotationStatus::class,
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'valid_until' => 'date',
            'revision_number' => 'integer',
            'sent_at' => 'datetime',
            'accepted_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(QuotationRequest::class, 'quotation_request_id');
    }

    public function salesLead(): BelongsTo
    {
        return $this->belongsTo(SalesLead::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(QuotationSection::class)->orderBy('sort_order');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(QuotationRevision::class)->orderByDesc('revision_number');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(QuotationApproval::class)->orderByDesc('created_at');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(QuotationDocument::class)->orderByDesc('created_at');
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(QuotationStatusHistory::class)->orderByDesc('created_at');
    }

    public function convertedProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'converted_project_id');
    }

    public function preparer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
