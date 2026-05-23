<?php

namespace App\Support;

use App\Models\ReviewAssignment;
use App\Models\Submission;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class EditorRoundReviews
{
    /**
     * Completed peer reviews for the submission's current round (editor decision workspace).
     *
     * @return Collection<int, array{
     *     assignment_id: string,
     *     reviewer_label: string,
     *     reviewer_name: string,
     *     recommendation: string,
     *     scores: string,
     *     comments_for_editor: string,
     *     submitted_at: Carbon|null
     * }>
     */
    public static function forCurrentRound(Submission $submission): Collection
    {
        $submission->loadMissing(['reviewAssignments.reviewer', 'reviewAssignments.review']);

        $roundVersion = (int) $submission->version;
        $assignments = $submission->reviewAssignments
            ->where('round_version', $roundVersion)
            ->filter(fn (ReviewAssignment $a) => $a->review !== null)
            ->sortBy('invited_at')
            ->values();

        $aliases = SubmissionPartyLabels::reviewerAliasesForRound($assignments);

        return $assignments->map(function (ReviewAssignment $assignment) use ($aliases) {
            $rev = $assignment->review;

            return [
                'assignment_id' => $assignment->id,
                'reviewer_label' => $aliases[$assignment->id] ?? 'Reviewer',
                'reviewer_name' => $assignment->reviewer->name,
                'recommendation' => str_replace('_', ' ', $rev->recommendation->value),
                'scores' => $rev->originality_score.'/'.$rev->methodology_score.'/'.$rev->clarity_score,
                'comments_for_editor' => trim((string) ($rev->comments_for_editor ?: $rev->comments_for_author)),
                'submitted_at' => $rev->submitted_at,
            ];
        });
    }
}
