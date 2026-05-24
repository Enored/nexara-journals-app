<?php

namespace App\Http\Controllers\Concerns;

use App\Enums\EditionStatus;
use App\Enums\JournalRole;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Collection;

trait LoadsSubmissionWorkspace
{
    /**
     * @return array{submission: Submission, reviewerPool: Collection<int, User>, editionsForPublish: Collection}
     */
    protected function loadSubmissionWorkspace(Submission $submission, bool $forEditor): array
    {
        $submission->load([
            'journal',
            'author',
            'edition.volume',
            'files.uploadedBy',
            'reviewAssignments' => fn ($q) => $q->orderByDesc('invited_at'),
            'reviewAssignments.reviewer',
            'reviewAssignments.editor',
            'reviewAssignments.review',
            'reviews.reviewer',
        ]);

        $reviewerPool = collect();
        $editionsForPublish = collect();

        if ($forEditor) {
            $reviewerPool = User::query()
                ->whereHas('journalUserRoles', function ($q) use ($submission) {
                    $q->where('journal_id', $submission->journal_id)
                        ->where('role', JournalRole::Reviewer);
                })
                ->orderBy('name')
                ->limit(100)
                ->get();

            if (auth()->user()?->can('publish', $submission)) {
                $editionsForPublish = $submission->journal->editions()
                    ->with('volume')
                    ->where('status', EditionStatus::Draft)
                    ->join('volumes', 'editions.volume_id', '=', 'volumes.id')
                    ->select('editions.*')
                    ->orderByDesc('volumes.number')
                    ->orderByDesc('editions.issue')
                    ->get();
            }
        }

        return [
            'submission' => $submission,
            'reviewerPool' => $reviewerPool,
            'editionsForPublish' => $editionsForPublish,
        ];
    }
}
