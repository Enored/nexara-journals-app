<?php

namespace App\Enums;

enum AnnouncementCategory: string
{
    case CallForPapers = 'call_for_papers';
    case SpecialIssue = 'special_issue';
    case Event = 'event';
    case Award = 'award';
    case Policy = 'policy';
    case Editorial = 'editorial';
    case SystemUpdate = 'system_update';
    case Milestone = 'milestone';

    public function label(): string
    {
        return match ($this) {
            self::CallForPapers => 'Call for Papers',
            self::SpecialIssue => 'Special Issue',
            self::Event => 'Event',
            self::Award => 'Award',
            self::Policy => 'Policy',
            self::Editorial => 'Editorial',
            self::SystemUpdate => 'System Update',
            self::Milestone => 'Milestone',
        };
    }
}
