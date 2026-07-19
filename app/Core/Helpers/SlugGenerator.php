<?php

namespace App\Core\Helpers;

use Illuminate\Support\Str;

final class SlugGenerator
{
    public static function make(string $value): string
    {
        return Str::slug($value);
    }
}
