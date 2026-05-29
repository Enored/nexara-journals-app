<?php

namespace App\Support;

use App\Models\Journal;

class AuthPagePayload
{
    /**
     * @return array<string, mixed>
     */
    public static function forLogin(): array
    {
        return [
            'pageTitle' => 'Sign in · '.platform_name(),
            'mode' => 'signin',
            ...self::shared(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function forRegister(): array
    {
        return [
            'pageTitle' => 'Register · '.platform_name(),
            'mode' => 'register',
            ...self::shared(),
            'countries' => config('countries'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function shared(): array
    {
        return [
            'press' => [
                'name' => platform_name(),
                'journals' => Journal::query()->where('is_active', true)->count(),
            ],
            'roles' => [
                'Researcher / Faculty',
                'Postdoctoral researcher',
                'PhD / Graduate student',
                'Undergraduate student',
                'Clinician',
                'Librarian',
                'Journalist',
                'Other',
            ],
        ];
    }
}
