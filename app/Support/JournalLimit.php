<?php

namespace App\Support;

use App\Models\Journal;

final class JournalLimit
{
    public static function max(): int
    {
        return max(1, (int) config('journal.max_journals', 5));
    }

    public static function count(): int
    {
        return Journal::query()->count();
    }

    public static function remaining(): int
    {
        return max(0, self::max() - self::count());
    }

    public static function canCreate(): bool
    {
        return self::count() < self::max();
    }

    public static function reachedMessage(): string
    {
        return 'You have reached the maximum of '.self::max().' journals. Contact support to increase your plan limit.';
    }
}
