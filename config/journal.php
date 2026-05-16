<?php

return [
    /*
    | Host used to detect apex vs journal subdomain (e.g. "nexarajournals.test" or "localhost").
    | Journal sites: {subdomain}.{base_domain}
    */
    'base_domain' => env('APP_BASE_DOMAIN', parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST) ?: 'localhost'),

    'reserved_subdomains' => ['www', 'api', 'mail', 'ftp', 'admin'],

    /*
    | Maximum journals per installation (billing / plan limit).
    */
    'max_journals' => (int) env('JOURNAL_MAX_JOURNALS', 5),
];
