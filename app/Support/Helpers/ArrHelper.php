<?php

namespace App\Support\Helpers;

final class ArrHelper
{
    /**
     * @param  array<string, mixed>  $array
     * @return array<string, mixed>
     */
    public static function withoutNulls(array $array): array
    {
        return array_filter($array, static fn ($value) => $value !== null);
    }
}
