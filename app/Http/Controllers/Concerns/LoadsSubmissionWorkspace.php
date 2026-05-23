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
            'edition',
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
                    ->where('status', EditionStatus::Draft)
                    ->orderByDesc('volume')
                    ->orderByDesc('issue')
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
