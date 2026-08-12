<?php

namespace App\Domains\Seo\Services;

use App\Core\Services\BaseService;
use App\Models\SeoRedirect;

final class RedirectService extends BaseService
{
    public function findActive(string $path): ?SeoRedirect
    {
        $normalized = '/'.ltrim($path, '/');

        return SeoRedirect::query()
            ->where('is_active', true)
            ->where(function ($query) use ($normalized, $path): void {
                $query->where('from_path', $normalized)->orWhere('from_path', $path);
            })
            ->first();
    }
}
