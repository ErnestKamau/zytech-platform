<?php

namespace App\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseModel extends Model
{
    use HasUuids;
    use SoftDeletes;

    public bool $incrementing = false;

    protected string $keyType = 'string';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [];
    }
}
