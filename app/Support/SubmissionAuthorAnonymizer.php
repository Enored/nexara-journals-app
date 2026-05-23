<?php

namespace App\Support;

use App\Enums\JournalRole;
use App\Models\Submission;
use App\Models\User;

final class SubmissionAuthorAnonymizer
{
    /**
     * Authors must not see reviewer/editor identities; editors, reviewers, and platform admins do.
     */
    public static function shouldAnonymize(User $user, Submission $submission): bool
    {
        if ($submission->author_id !== $user->id) {
            return false;
        }

        if ($user->isPlatformAdmin()) {
            return false;
        }

        if ($user->hasJournalRole($submission->journal, JournalRole::Editor)
            || $user->hasJournalRole($submission->journal, JournalRole::Admin)) {
            return false;
        }

        return true;
    }

    public static function apply(Submission $submission): Submission
    {
        $submission->loadMissing([
            'reviewAssignments',
            'reviews',
            'editorialDecisions',
        ]);

        $byRound = $submission->reviewAssignments->groupBy(
            fn ($a) => (int) ($a->round_version ?? 1)
        );

        $submission->setRelation(
            'reviewAssignments',
            $submission->reviewAssignments->map(function ($assignment) use ($byRound) {
                $round = $byRound->get((int) ($assignment->round_version ?? 1), collect());
                $assignment->unsetRelation('reviewer');
                $assignment->unsetRelation('editor');
                $assignment->setAttribute(
                    'reviewer_label',
                    SubmissionPartyLabels::reviewerLabelForAssignment($round, $assignment)
                );

                return $assignment;
            })
        );

        $submission->setRelation(
            'reviews',
            $submission->reviews->map(function ($review) use ($submission, $byRound) {
                $assignment = $submission->reviewAssignments->firstWhere('id', $review->assignment_id);
                $round = $assignment
                    ? $byRound->get((int) ($assignment->round_version ?? 1), collect())
                    : collect();
                $review->unsetRelation('reviewer');
                $review->makeHidden([
                    'reviewer_id',
                    'comments_for_author',
                    'comments_for_editor',
                    'originality_score',
                    'methodology_score',
                    'clarity_score',
                    'recommendation',
                ]);

                return $review;
            })
        );

        $submission->setRelation(
            'editorialDecisions',
            $submission->editorialDecisions->map(function ($decision) {
                $decision->unsetRelation('recorder');
                $decision->makeHidden(['recorded_by']);
                $decision->setAttribute('party_label', SubmissionPartyLabels::EDITOR);

                return $decision;
            })
        );

        return $submission;
    }
}
