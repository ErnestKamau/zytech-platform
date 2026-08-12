<?php

namespace App\Models;

use App\Core\Enums\ApprovalStatus;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationApproval extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'quotation_id',
        'status',
        'notes',
        'reviewer_id',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ApprovalStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
