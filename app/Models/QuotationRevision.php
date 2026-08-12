<?php

namespace App\Models;

use App\Core\Enums\RevisionStatus;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationRevision extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'quotation_id',
        'revision_number',
        'status',
        'summary',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'revision_number' => 'integer',
            'status' => RevisionStatus::class,
        ];
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
