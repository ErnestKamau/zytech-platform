<?php

namespace App\Models;

use App\Core\Enums\FollowUpStatus;
use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUp extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'client_id',
        'quotation_request_id',
        'order_id',
        'sales_order_id',
        'assigned_to',
        'due_at',
        'status',
        'notes',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => FollowUpStatus::class,
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function quotationRequest(): BelongsTo
    {
        return $this->belongsTo(QuotationRequest::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
