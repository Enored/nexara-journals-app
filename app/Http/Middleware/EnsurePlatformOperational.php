<?php

namespace App\Http\Middleware;

use App\Models\PlatformSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlatformOperational
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! PlatformSetting::isMaintenanceMode()) {
            return $next($request);
        }

        if ($this->canBypassMaintenance($request)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'The platform is temporarily unavailable for maintenance.',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->view('maintenance', [
            'platformName' => platform_name(),
        ], Response::HTTP_SERVICE_UNAVAILABLE);
    }

    private function canBypassMaintenance(Request $request): bool
    {
        if ($request->user()?->isPlatformAdmin()) {
            return true;
        }

        return $request->routeIs('login', 'admin.settings.edit', 'admin.settings.update');
    }
}
