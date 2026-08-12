<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectBeforeAfter extends BaseModel
{
    use HasActivity;

    protected $table = 'project_before_after';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_id',
        'before_image_key',
        'after_image_key',
        'caption',
        'description',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
