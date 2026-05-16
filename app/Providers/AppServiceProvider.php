<?php

namespace App\Providers;

use App\Support\SessionCookieDomain;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $domain = SessionCookieDomain::resolve();
        if ($domain !== null) {
            config(['session.domain' => $domain]);
        }

        $this->configureSanctumStatefulDomains();
    }

    private function configureSanctumStatefulDomains(): void
    {
        $base = (string) config('journal.base_domain', '');
        if ($base === '') {
            return;
        }

        $port = parse_url((string) config('app.url'), PHP_URL_PORT);
        $portSuffix = $port ? ':'.$port : '';

        $extra = array_filter([
            $base,
            $base.$portSuffix,
            'www.'.$base,
            'www.'.$base.$portSuffix,
            Sanctum::currentApplicationUrlWithPort(),
            Sanctum::currentRequestHost(),
        ]);

        config([
            'sanctum.stateful' => array_values(array_unique(array_merge(
                config('sanctum.stateful', []),
                $extra
            ))),
        ]);
    }
}
