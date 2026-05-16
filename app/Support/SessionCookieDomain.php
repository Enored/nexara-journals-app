<?php

namespace App\Support;

class SessionCookieDomain
{
    /**
     * Cookie Domain attribute for apex + journal subdomains (no leading dot).
     */
    public static function resolve(): ?string
    {
        $explicit = env('SESSION_DOMAIN');

        if (is_string($explicit) && $explicit !== '' && ! in_array(strtolower($explicit), ['null', 'false'], true)) {
            return ltrim($explicit, '.') ?: null;
        }

        $base = (string) config('journal.base_domain', '');

        if ($base === '') {
            return null;
        }

        return ltrim($base, '.');
    }

    /**
     * Domains where shared cookies are unreliable; prefer lvh.me or *.test locally.
     */
    public static function isUnreliableForSharedCookies(string $baseDomain): bool
    {
        $base = ltrim($baseDomain, '.');

        if (filter_var($base, FILTER_VALIDATE_IP) !== false) {
            return true;
        }

        return str_ends_with($base, '.nip.io') || $base === 'nip.io';
    }
}
