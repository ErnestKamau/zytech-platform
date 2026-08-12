<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddPublicCacheHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (
            $request->isMethod('GET')
            && $request->user() === null
            && $response->isSuccessful()
            && ! $request->is('admin*', 'portal*', 'account*', 'livewire*', 'login', 'register')
        ) {
            $response->headers->set('Cache-Control', 'public, max-age=60, stale-while-revalidate=300');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        }

        return $response;
    }
}
