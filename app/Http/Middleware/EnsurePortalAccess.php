<?php

namespace App\Http\Middleware;

use App\Domains\Configuration\Services\FeatureFlagService;
use App\Domains\Portal\Repositories\PortalRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsurePortalAccess
{
    public function __construct(
        private readonly PortalRepository $portal,
        private readonly FeatureFlagService $flags,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($this->flags->enabled('client_portal'), 404);

        $user = $request->user();
        abort_unless($user !== null, 403);

        $client = $this->portal->clientForUser($user);
        abort_unless($client !== null, 403, 'Portal access has not been granted for this account.');

        $request->attributes->set('portalClient', $client);

        return $next($request);
    }
}
