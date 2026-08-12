<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'quotation_id',
        'quotation_section_id',
        'label',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'line_total',
        'is_optional',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
            'is_optional' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(QuotationSection::class, 'quotation_section_id');
    }
}
