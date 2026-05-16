<?php

namespace App\Http\Middleware;

use App\Models\Journal;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ResolveJournalFromHost
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $base = (string) config('journal.base_domain');

        if ($host === $base || $host === 'www.'.$base) {
            $request->attributes->set('current_journal', null);
            View::share('currentJournal', null);

            return $next($request);
        }

        $suffix = '.'.$base;
        if (! str_ends_with($host, $suffix)) {
            $request->attributes->set('current_journal', null);
            View::share('currentJournal', null);

            return $next($request);
        }

        $subdomain = substr($host, 0, -strlen($suffix));
        if ($subdomain === '' || $subdomain === 'www' || in_array($subdomain, config('journal.reserved_subdomains'), true)) {
            abort(404);
        }

        $journal = Journal::query()
            ->where('subdomain', $subdomain)
            ->where('is_active', true)
            ->first();

        if (! $journal) {
            abort(404);
        }

        $request->attributes->set('current_journal', $journal);
        View::share('currentJournal', $journal);

        return $next($request);
    }
}
