<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProformaInvoiceDocument extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'proforma_invoice_id',
        'title',
        'kind',
        'stored_path',
        'mime_type',
        'size_bytes',
        'verification_code',
    ];

    protected function casts(): array
    {
        return ['size_bytes' => 'integer'];
    }

    public function proformaInvoice(): BelongsTo
    {
        return $this->belongsTo(ProformaInvoice::class);
    }
}
