<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationStatusHistory extends BaseModel
{
    protected $table = 'quotation_status_history';

    /** @var list<string> */
    protected $fillable = [
        'quotation_request_id',
        'quotation_id',
        'from_status',
        'to_status',
        'notes',
        'changed_by',
    ];

    public function quotationRequest(): BelongsTo
    {
        return $this->belongsTo(QuotationRequest::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
