<?php

namespace App\Support;

use App\Enums\JournalRole;
use App\Models\Submission;
use App\Models\User;

class SubmissionWorkspace
{
    public static function authorRoute(Submission $submission): string
    {
        return platform_route('author.submissions.show', $submission);
    }

    public static function editorRoute(Submission $submission): string
    {
        return platform_route('editor.submissions.show', $submission);
    }

    /**
     * Best workspace URL for the current user, or null if they have no workspace access.
     */
    public static function routeFor(User $user, Submission $submission): ?string
    {
        if ($submission->author_id === $user->id) {
            return self::authorRoute($submission);
        }

        if ($user->isPlatformAdmin()
            || $user->hasJournalRole($submission->journal, JournalRole::Editor)
            || $user->hasJournalRole($submission->journal, JournalRole::Admin)) {
            return self::editorRoute($submission);
        }

        return null;
    }
}
