<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientAddress extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'client_id', 'label', 'line1', 'line2', 'city', 'county', 'country', 'is_primary', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean', 'sort_order' => 'integer'];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function summary(): string
    {
        return collect([$this->line1, $this->line2, $this->city, $this->county, $this->country])
            ->filter()
            ->implode(', ');
    }
}
