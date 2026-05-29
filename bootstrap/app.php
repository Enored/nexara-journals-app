<?php

use App\Http\Middleware\EnsureJournalContext;
use App\Http\Middleware\EnsurePlatformOperational;
use App\Http\Middleware\EnsurePlatformAdmin;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\RedirectPlatformRoutesToApex;
use App\Http\Middleware\ResolveJournalFromHost;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn () => platform_route('login'));

        $middleware->alias([
            'journal.resolve' => ResolveJournalFromHost::class,
            'platform.admin' => EnsurePlatformAdmin::class,
            'journal.context' => EnsureJournalContext::class,
        ]);
        $middleware->web(append: [
            ResolveJournalFromHost::class,
            RedirectPlatformRoutesToApex::class,
            EnsurePlatformOperational::class,
            HandleInertiaRequests::class,
        ]);
        $middleware->api(prepend: [
            ResolveJournalFromHost::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
