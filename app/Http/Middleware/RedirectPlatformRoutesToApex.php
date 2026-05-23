<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Auth, dashboards, and cross-journal workflow live on the apex host only.
 */
class RedirectPlatformRoutesToApex
{
    /** @var list<string> */
    private const PLATFORM_PREFIXES = [
        '/login',
        '/register',
        '/dashboard',
        '/settings',
        '/admin',
        '/author',
        '/editor',
        '/reviewer',
        '/submissions',
        '/review-tasks',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (is_platform_host()) {
            return $next($request);
        }

        $path = '/'.ltrim($request->path(), '/');
        if ($path === '/') {
            return $next($request);
        }

        if ($request->isMethod('POST') && $path === '/logout') {
            return $this->redirectToApex($request);
        }

        foreach (self::PLATFORM_PREFIXES as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return $this->redirectToApex($request);
            }
        }

        return $next($request);
    }

    private function redirectToApex(Request $request): Response
    {
        $target = platform_url($request->getRequestUri());

        return redirect()->away($target, $request->isMethod('GET') ? 302 : 307);
    }
}
