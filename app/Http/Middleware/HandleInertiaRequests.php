<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $journal = current_journal();

        return [
            ...parent::share($request),
            'siteContext' => $journal ? 'journal' : 'platform',
            'journal' => $journal ? [
                'name' => $journal->name,
            ] : null,
            'auth' => [
                'user' => $user ? [
                    'name' => $user->name,
                    'firstName' => $user->first_name,
                ] : null,
            ],
            'platform' => [
                'name' => platform_name(),
                'urls' => [
                    'home' => platform_url('/'),
                    'journals' => platform_url('/#journals'),
                    'blogs' => platform_route('blogs.index'),
                    'about' => platform_route('about'),
                    'login' => platform_route('login'),
                    'dashboard' => platform_route('dashboard'),
                    'settings' => platform_route('settings.edit'),
                    'logout' => platform_route('logout'),
                ],
            ],
        ];
    }
}
