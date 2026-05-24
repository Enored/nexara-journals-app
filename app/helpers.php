<?php

use App\Models\Journal;
use Illuminate\Http\Request;

function current_journal(): ?Journal
{
    /** @var Request $request */
    $request = request();

    return $request?->attributes->get('current_journal');
}

/**
 * Absolute URL for a journal subdomain path (uses APP_URL scheme/port).
 */
function journal_front_url(Journal $journal, string $path = '/'): string
{
    $base = (string) config('journal.base_domain');
    $path = str_starts_with($path, '/') ? $path : '/'.$path;
    $appUrl = (string) config('app.url');
    $scheme = parse_url($appUrl, PHP_URL_SCHEME) ?: 'http';
    $port = parse_url($appUrl, PHP_URL_PORT);
    $host = $journal->subdomain.'.'.$base;
    $authority = $host;
    if ($port && ! in_array((int) $port, [80, 443], true)) {
        $authority .= ':'.$port;
    }

    return $scheme.'://'.$authority.$path;
}

function is_platform_host(): bool
{
    $host = request()->getHost();
    $base = (string) config('journal.base_domain');

    return $host === $base || $host === 'www.'.$base;
}

/**
 * Absolute URL on the apex / platform host (APP_BASE_DOMAIN).
 */
function platform_url(string $path = '/'): string
{
    $base = (string) config('journal.base_domain');
    $path = str_starts_with($path, '/') ? $path : '/'.$path;
    $appUrl = (string) config('app.url');
    $scheme = parse_url($appUrl, PHP_URL_SCHEME) ?: 'http';
    $port = parse_url($appUrl, PHP_URL_PORT);
    $authority = $base;
    if ($port && ! in_array((int) $port, [80, 443], true)) {
        $authority .= ':'.$port;
    }

    return $scheme.'://'.$authority.$path;
}

/**
 * Named route URL always on the platform host (for login, dashboards, etc.).
 */
function platform_route(string $name, mixed $parameters = [], bool $absolute = true): string
{
    $relative = route($name, $parameters, false);
    $path = parse_url($relative, PHP_URL_PATH) ?? '/';
    $query = parse_url($relative, PHP_URL_QUERY);
    if ($query !== null && $query !== '') {
        $path .= '?'.$query;
    }

    return platform_url($path);
}

/**
 * Author or editor workspace URL for a submission, or null when the user has no workspace access.
 */
function submission_workspace_route(\App\Models\Submission $submission, ?\App\Models\User $user = null): ?string
{
    $user ??= auth()->user();

    if (! $user) {
        return null;
    }

    return \App\Support\SubmissionWorkspace::routeFor($user, $submission);
}

/**
 * Public URL for UBold dashboard static assets (copied from use_this_UI_for_dashboards/dist/assets).
 */
function ubold_asset(string $path = ''): string
{
    $path = ltrim($path, '/');

    return asset('vendor/ubold/'.$path);
}
