<?php

namespace App\Enums;

enum JournalRole: string
{
    case Author = 'author';
    case Reviewer = 'reviewer';
    case Editor = 'editor';
    case Admin = 'admin';

    /**
     * Per-journal staff roles assigned in admin. Author capability is implicit for every account.
     *
     * @return list<self>
     */
    public static function assignable(): array
    {
        return [
            self::Reviewer,
            self::Editor,
            self::Admin,
        ];
    }

    public function isAssignable(): bool
    {
        return $this !== self::Author;
    }

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Journal admin',
            self::Editor => 'Editor',
            self::Reviewer => 'Reviewer',
            self::Author => 'Author',
        };
    }
}
