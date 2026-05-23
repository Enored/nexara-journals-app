<?php

namespace App\Support;

use App\Models\ReviewAssignment;
use Illuminate\Support\Collection;

final class SubmissionPartyLabels
{
    public const EDITOR = 'Editor';

    /**
     * Stable anonymous labels for reviewers on a round (by invitation order).
     *
     * @param  Collection<int, ReviewAssignment>  $assignments
     * @return array<string, string> assignment id => "Reviewer N"
     */
    public static function reviewerAliasesForRound(Collection $assignments): array
    {
        $aliases = [];
        $n = 0;

        foreach ($assignments->sortBy('invited_at')->values() as $assignment) {
            $n++;
            $aliases[$assignment->id] = 'Reviewer '.$n;
        }

        return $aliases;
    }

    public static function reviewerLabelForAssignment(Collection $assignments, ReviewAssignment $assignment): string
    {
        return self::reviewerAliasesForRound($assignments)[$assignment->id] ?? 'Reviewer';
    }
}
