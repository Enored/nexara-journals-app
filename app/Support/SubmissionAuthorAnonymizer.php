<?php

namespace App\Support;

use App\Enums\JournalRole;
use App\Enums\ReviewModel;
use App\Models\Submission;
use App\Models\User;

final class SubmissionAuthorAnonymizer
{
    /**
     * The review model that governs identity disclosure for a submission,
     * read from the owning journal (eager-loaded; no extra query) and falling
     * back to Single-Blind when unset.
     */
    public static function reviewModelFor(Submission $submission): ReviewModel
    {
        $model = $submission->journal?->review_model;

        return $model instanceof ReviewModel ? $model : ReviewModel::SingleBlind;
    }

    /**
     * Apply the identity blinding appropriate to the given viewer and the
     * submission's journal review model. Mutates and returns the submission.
     *
     * Editors of the journal and platform admins always see full identities.
     */
    public static function forViewer(User $user, Submission $submission): Submission
    {
        if ($user->isPlatformAdmin()
            || $user->hasJournalRole($submission->journal, JournalRole::Editor)
            || $user->hasJournalRole($submission->journal, JournalRole::Admin)) {
            return $submission;
        }

        $model = self::reviewModelFor($submission);

        if ($submission->author_id === $user->id) {
            if ($model->hidesReviewersFromAuthor()) {
                self::applyForAuthor($submission);
            }

            return $submission;
        }

        if ($model->hidesAuthorFromReviewer()) {
            self::applyForReviewer($submission);
        }

        return $submission;
    }

    /**
     * Hide reviewer and editor identities from the author (single- and
     * double-blind), replacing them with stable anonymous labels.
     */
    public static function applyForAuthor(Submission $submission): Submission
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

    /**
     * Hide the author identity from a reviewer (double-blind only) while
     * keeping the manuscript content the reviewer needs. Also strips
     * author-identifying files from the loaded files relation so reviewers
     * never receive the non-anonymized manuscript.
     */
    public static function applyForReviewer(Submission $submission): Submission
    {
        $submission->unsetRelation('author');
        $submission->makeHidden(['author_id']);

        if ($submission->relationLoaded('files')) {
            $submission->setRelation(
                'files',
                SubmissionFileAccess::reviewerSafeFiles($submission->files)
            );
        }

        return $submission;
    }
}
