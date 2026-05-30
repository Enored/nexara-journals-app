<?php

namespace App\Enums;

enum AnnouncementScope: string
{
    case Global = 'global';
    case PerJournal = 'per_journal';

    public function label(): string
    {
        return match ($this) {
            self::Global => 'Global',
            self::PerJournal => 'Per journal',
        };
    }
}
