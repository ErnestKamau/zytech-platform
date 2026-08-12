<?php

namespace App\Http\Middleware;

use App\Core\Enums\RoleType;
use App\Domains\Configuration\Services\FeatureFlagService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class CheckPlatformMaintenance
{
    public function __construct(
        private readonly FeatureFlagService $flags,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        try {
            $maintenance = $this->flags->enabled('maintenance_mode');
        } catch (\Throwable) {
            return $next($request);
        }

        if (! $maintenance) {
            return $next($request);
        }

        if ($request->is('admin', 'admin/*', 'login', 'up')) {
            return $next($request);
        }

        $user = $request->user();

        if ($user !== null && $user->hasAnyRole([
            RoleType::SuperAdmin->value,
            RoleType::Administrator->value,
            RoleType::Staff->value,
        ])) {
            return $next($request);
        }

        abort(503, 'The platform is temporarily unavailable.');
    }
}
