<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleDownload extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = ['article_id', 'title', 'description', 'file_key', 'external_url', 'sort_order'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
