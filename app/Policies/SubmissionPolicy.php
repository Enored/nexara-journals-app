<?php

namespace App\Policies;

use App\Enums\JournalRole;
use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    public function view(User $user, Submission $submission): bool
    {
        return $this->viewAsAuthor($user, $submission)
            || $this->viewAsEditor($user, $submission);
    }

    public function viewAsAuthor(User $user, Submission $submission): bool
    {
        return $submission->author_id === $user->id;
    }

    public function viewAsEditor(User $user, Submission $submission): bool
    {
        if ($user->isPlatformAdmin()) {
            return true;
        }

        return $user->hasJournalRole($submission->journal, JournalRole::Editor)
            || $user->hasJournalRole($submission->journal, JournalRole::Admin);
    }

    public function assignReviewer(User $user, Submission $submission): bool
    {
        if (! $this->editorCanManageWorkflow($user, $submission)) {
            return false;
        }

        return in_array($submission->status, [
            SubmissionStatus::Submitted,
            SubmissionStatus::UnderReview,
        ], true);
    }

    public function recordDecision(User $user, Submission $submission): bool
    {
        if (! $this->editorCanManageWorkflow($user, $submission)) {
            return false;
        }

        return in_array($submission->status, [
            SubmissionStatus::Submitted,
            SubmissionStatus::UnderReview,
        ], true);
    }

    private function editorCanManageWorkflow(User $user, Submission $submission): bool
    {
        if ($user->isPlatformAdmin()) {
            return true;
        }

        return $user->hasJournalRole($submission->journal, JournalRole::Editor)
            || $user->hasJournalRole($submission->journal, JournalRole::Admin);
    }

    public function submitRevision(User $user, Submission $submission): bool
    {
        return $submission->author_id === $user->id
            && $submission->status === SubmissionStatus::RevisionRequested;
    }

    public function publish(User $user, Submission $submission): bool
    {
        if ($submission->status !== SubmissionStatus::Accepted) {
            return false;
        }

        if ($user->isPlatformAdmin()) {
            return true;
        }

        return $user->hasJournalRole($submission->journal, JournalRole::Editor)
            || $user->hasJournalRole($submission->journal, JournalRole::Admin);
    }
}
