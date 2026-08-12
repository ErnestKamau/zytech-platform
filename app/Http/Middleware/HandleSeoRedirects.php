<?php

namespace App\Http\Middleware;

use App\Domains\Seo\Services\RedirectService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class HandleSeoRedirects
{
    public function __construct(private readonly RedirectService $redirects) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('GET')) {
            return $next($request);
        }

        $redirect = $this->redirects->findActive($request->getPathInfo());

        if ($redirect === null) {
            return $next($request);
        }

        return redirect($redirect->to_path, $redirect->status_code);
    }
}
