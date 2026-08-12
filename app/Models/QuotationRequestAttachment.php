<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationRequestAttachment extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'quotation_request_id',
        'original_name',
        'stored_path',
        'mime_type',
        'size_bytes',
    ];

    protected function casts(): array
    {
        return ['size_bytes' => 'integer'];
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(QuotationRequest::class, 'quotation_request_id');
    }
}
