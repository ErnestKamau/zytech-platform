<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchQuery extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'query',
        'context',
        'user_id',
        'result_count',
    ];

    protected function casts(): array
    {
        return [
            'result_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
