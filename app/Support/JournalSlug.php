<?php

namespace App\Support;

use App\Models\Journal;
use Illuminate\Http\Request;

final class JournalSlug
{
    public const QUERY_KEY = 'journal';

    public static function normalize(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return strtolower(trim($value));
    }

    /**
     * @param  list<string>|null  $allowedSubdomains
     */
    public static function fromRequest(Request $request, ?array $allowedSubdomains = null): ?string
    {
        $subdomain = null;

        if ($request->filled(self::QUERY_KEY)) {
            $subdomain = self::normalize($request->string(self::QUERY_KEY)->toString());
        } elseif ($request->filled('journal_id')) {
            $legacy = Journal::query()->find($request->string('journal_id')->toString());
            $subdomain = self::normalize($legacy?->subdomain);
        }

        if ($subdomain === null) {
            return null;
        }

        if ($allowedSubdomains !== null && ! in_array($subdomain, $allowedSubdomains, true)) {
            return null;
        }

        return $subdomain;
    }

    public static function resolveId(?string $subdomain): ?string
    {
        if ($subdomain === null) {
            return null;
        }

        return Journal::query()->where('subdomain', $subdomain)->value('id');
    }

    public static function find(?string $subdomain): ?Journal
    {
        if ($subdomain === null) {
            return null;
        }

        return Journal::query()->where('subdomain', $subdomain)->first();
    }
}
