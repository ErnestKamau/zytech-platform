<?php

namespace App\Models;

use App\Core\Enums\SiteVisitStatus;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteVisit extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'quotation_request_id',
        'sales_lead_id',
        'visit_type',
        'status',
        'scheduled_at',
        'location',
        'engineer_name',
        'notes',
        'recommendations',
        'scheduled_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => SiteVisitStatus::class,
            'scheduled_at' => 'datetime',
        ];
    }

    public function quotationRequest(): BelongsTo
    {
        return $this->belongsTo(QuotationRequest::class);
    }

    public function salesLead(): BelongsTo
    {
        return $this->belongsTo(SalesLead::class);
    }

    public function scheduler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scheduled_by');
    }
}
