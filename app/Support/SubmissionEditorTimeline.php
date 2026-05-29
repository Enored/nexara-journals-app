<?php

namespace App\Support;

use App\Enums\ReviewAssignmentStatus;
use App\Enums\SubmissionStatus;
use App\Models\ReviewAssignment;
use App\Models\Submission;
use App\Models\SubmissionVersion;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SubmissionEditorTimeline
{
    /**
     * @return array{
     *     original_title: string,
     *     versions: list<array{
     *         version: int,
     *         title: string,
     *         abstract: string,
     *         keywords: array,
     *         submitted_at: Carbon,
     *         files: Collection,
     *         events: list<array{kind: string, label: string, at: Carbon, meta: array}>
     *     }>,
     *     published: ?array{at: Carbon, url: string}
     * }
     */
    public static function build(Submission $submission, bool $forAuthor = false): array
    {
        $submission->loadMissing([
            'versions',
            'files.uploadedBy',
            'reviewAssignments.reviewer',
            'reviewAssignments.editor',
            'reviewAssignments.review',
            'editorialDecisions.recorder',
            'journal',
        ]);

        $filesByVersion = $submission->files->groupBy('version');
        $versionNumbers = self::versionNumbers($submission, $filesByVersion);

        $snapshots = $submission->versions->keyBy('version');
        $originalTitle = $snapshots->get(1)?->title ?? $submission->title;

        $versions = [];
        foreach ($versionNumbers as $version) {
            $snapshot = $snapshots->get($version);
            $files = $filesByVersion->get($version, collect());

            $submittedAt = $snapshot?->submitted_at
                ?? $files->min('created_at')
                ?? $submission->created_at;

            $versions[] = [
                'version' => $version,
                'title' => $snapshot?->title ?? $submission->title,
                'abstract' => $snapshot?->abstract ?? $submission->abstract,
                'keywords' => $snapshot?->keywords ?? $submission->keywords ?? [],
                'submitted_at' => Carbon::parse($submittedAt),
                'files' => $files,
                'events' => self::eventsForVersion($submission, $version, $snapshot, $forAuthor),
            ];
        }

        $published = null;
        if ($submission->status === SubmissionStatus::Published) {
            $published = [
                'at' => $submission->updated_at,
                'url' => journal_front_url($submission->journal, '/articles/'.$submission->id),
            ];
        }

        return [
            'original_title' => $originalTitle,
            'versions' => $versions,
            'published' => $published,
        ];
    }

    /**
     * Manuscript snapshot and activity for a single review assignment (reviewer workspace).
     *
     * @return array{versions: list<array>}
     */
    public static function buildForReviewer(ReviewAssignment $assignment): array
    {
        $assignment->loadMissing(['review', 'submission.journal', 'submission.versions']);

        $submission = $assignment->submission;
        $roundVersion = max(1, (int) $assignment->round_version);

        $full = self::build($submission);
        $round = collect($full['versions'])->firstWhere('version', $roundVersion);

        if (! $round) {
            $submission->loadMissing(['files']);
            $files = $submission->files->where('version', $roundVersion);
            $snapshot = $submission->versions->firstWhere('version', $roundVersion);
            $submittedAt = $snapshot?->submitted_at
                ?? $files->min('created_at')
                ?? $submission->created_at;

            $round = [
                'version' => $roundVersion,
                'title' => $snapshot?->title ?? $submission->title,
                'abstract' => $snapshot?->abstract ?? $submission->abstract,
                'keywords' => $snapshot?->keywords ?? $submission->keywords ?? [],
                'submitted_at' => Carbon::parse($submittedAt),
                'files' => $files,
                'events' => [],
            ];
        }

        $round['events'] = self::eventsForReviewerAssignment($assignment, $roundVersion, $submission);

        return ['versions' => [$round]];
    }

    /**
     * @return list<array{kind: string, label: string, at: Carbon, meta: array}>
     */
    private static function eventsForReviewerAssignment(
        ReviewAssignment $assignment,
        int $version,
        Submission $submission,
    ): array {
        $events = [];

        $snapshot = $submission->versions->firstWhere('version', $version);
        if ($version > 1 && $snapshot?->submitted_at) {
            $events[] = [
                'kind' => 'author_revision_submitted',
                'label' => 'Author submitted revision (version '.$version.')',
                'at' => Carbon::parse($snapshot->submitted_at),
                'meta' => [],
            ];
        }

        if ($assignment->invited_at) {
            $events[] = [
                'kind' => 'reviewer_assigned',
                'label' => 'You were assigned to review',
                'at' => Carbon::parse($assignment->invited_at),
                'meta' => [
                    'deadline' => $assignment->deadline->format('M j, Y'),
                ],
            ];
        }

        if ($assignment->review) {
            $rev = $assignment->review;
            $events[] = [
                'kind' => 'review_submitted',
                'label' => 'You submitted your review',
                'at' => Carbon::parse($rev->submitted_at ?? $assignment->completed_at ?? now()),
                'meta' => [
                    'recommendation' => str_replace('_', ' ', $rev->recommendation->value),
                    'scores' => $rev->originality_score.'/'.$rev->methodology_score.'/'.$rev->clarity_score,
                    'comments_for_editor' => trim((string) ($rev->comments_for_editor ?: $rev->comments_for_author)),
                ],
            ];
        }

        usort($events, fn ($a, $b) => $a['at'] <=> $b['at']);

        return $events;
    }

    /**
     * @param  Collection<int, Collection>  $filesByVersion
     * @return list<int>
     */
    private static function versionNumbers(Submission $submission, Collection $filesByVersion): array
    {
        $fromSnapshots = $submission->versions->pluck('version');
        $fromFiles = $filesByVersion->keys();
        $max = max(
            (int) $submission->version,
            (int) $fromSnapshots->max() ?: 0,
            (int) $fromFiles->max() ?: 0,
            1
        );

        return collect(range(1, $max))
            ->merge($fromSnapshots)
            ->merge($fromFiles)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return list<array{kind: string, label: string, at: Carbon, meta: array}>
     */
    private static function eventsForVersion(
        Submission $submission,
        int $version,
        ?SubmissionVersion $snapshot = null,
        bool $forAuthor = false,
    ): array {
        $events = [];
        $roundAssignments = $submission->reviewAssignments->where('round_version', $version);

        if ($version > 1 && $snapshot?->submitted_at) {
            $events[] = [
                'kind' => 'author_revision_submitted',
                'label' => $forAuthor
                    ? 'You submitted revision (version '.$version.')'
                    : 'Author submitted revision (version '.$version.')',
                'at' => Carbon::parse($snapshot->submitted_at),
                'meta' => [],
            ];
        }

        foreach ($roundAssignments as $assignment) {
            if (! $forAuthor && $assignment->invited_at) {
                $events[] = [
                    'kind' => 'reviewer_assigned',
                    'label' => 'Assigned '.$assignment->reviewer->name.' to review',
                    'at' => Carbon::parse($assignment->invited_at),
                    'meta' => [
                        'reviewer' => $assignment->reviewer->name,
                        'deadline' => $assignment->deadline->format('M j, Y'),
                    ],
                ];
            }

            if ($assignment->review && ! $forAuthor) {
                $rev = $assignment->review;
                $confidentialComments = trim((string) ($rev->comments_for_editor ?: $rev->comments_for_author));

                $events[] = [
                    'kind' => 'review_submitted',
                    'label' => 'Review submitted by '.$assignment->reviewer->name,
                    'at' => Carbon::parse($rev->submitted_at ?? $assignment->completed_at),
                    'meta' => [
                        'recommendation' => str_replace('_', ' ', $rev->recommendation->value),
                        'scores' => $rev->originality_score.'/'.$rev->methodology_score.'/'.$rev->clarity_score,
                        'comments_for_editor' => $confidentialComments,
                    ],
                ];
            }
        }

        foreach ($submission->editorialDecisions->where('version', $version) as $decision) {
            $events[] = [
                'kind' => 'editorial_decision',
                'label' => $forAuthor
                    ? SubmissionPartyLabels::EDITOR.': '.self::formatDecisionLabel($decision->decision)
                    : 'Editorial decision: '.self::formatDecisionLabel($decision->decision),
                'at' => Carbon::parse($decision->created_at),
                'meta' => $forAuthor ? [
                    'party_label' => SubmissionPartyLabels::EDITOR,
                    'decision' => $decision->decision,
                    'decision_letter' => $decision->decision_letter,
                ] : [
                    'decision' => $decision->decision,
                    'decision_letter' => $decision->decision_letter,
                    'recorded_by' => $decision->recorder?->name,
                ],
            ];
        }

        // Legacy: decision stored on submission row before history table (attribute to version 1 only)
        if ($submission->editorialDecisions->isEmpty()
            && $version === 1
            && $submission->decision_letter) {
            $events[] = [
                'kind' => 'editorial_decision',
                'label' => $forAuthor ? SubmissionPartyLabels::EDITOR.' decision' : 'Editorial decision',
                'at' => Carbon::parse($submission->updated_at),
                'meta' => $forAuthor ? [
                    'party_label' => SubmissionPartyLabels::EDITOR,
                    'decision' => '',
                    'decision_letter' => $submission->decision_letter,
                ] : [
                    'decision' => '',
                    'decision_letter' => $submission->decision_letter,
                    'recorded_by' => null,
                ],
            ];
        }

        usort($events, fn ($a, $b) => $a['at'] <=> $b['at']);

        return $events;
    }

    public static function formatDecisionLabel(string $decision): string
    {
        return match ($decision) {
            'accept', 'accepted' => 'Accept',
            'minor_revision', 'revision_requested' => 'Revision requested',
            'major_revision' => 'Major revision requested',
            'reject', 'rejected' => 'Reject',
            'submitted' => 'Submitted',
            default => ucfirst(str_replace('_', ' ', $decision)),
        };
    }
}
