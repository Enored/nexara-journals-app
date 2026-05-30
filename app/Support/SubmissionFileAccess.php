<?php

namespace App\Support;

use App\Enums\JournalRole;
use App\Enums\SubmissionFileType;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Decides which submission files a given viewer may see and download, honoring
 * the owning journal's review model. Under double-blind, reviewers are limited
 * to author-safe files (the blinded manuscript and supplementary material);
 * editors, journal admins, platform admins, and the author always see everything.
 */
final class SubmissionFileAccess
{
    /** File types a double-blind reviewer is allowed to see. */
    private const REVIEWER_SAFE_TYPES = [
        SubmissionFileType::BlindedManuscript,
        SubmissionFileType::Supplementary,
    ];

    /**
     * The subset of a submission's files visible to the viewer.
     *
     * @return Collection<int, SubmissionFile>
     */
    public static function visibleFiles(User $user, Submission $submission): Collection
    {
        $files = $submission->files instanceof Collection
            ? $submission->files
            : $submission->files()->get();

        if (self::seesEverything($user, $submission)) {
            return $files;
        }

        if (SubmissionAuthorAnonymizer::reviewModelFor($submission)->hidesAuthorFromReviewer()) {
            return $files
                ->filter(fn (SubmissionFile $file) => in_array($file->file_type, self::REVIEWER_SAFE_TYPES, true))
                ->values();
        }

        return $files;
    }

    /**
     * Whether the viewer may download a specific file.
     */
    public static function canDownload(User $user, SubmissionFile $file): bool
    {
        $submission = $file->submission;

        if (self::seesEverything($user, $submission)) {
            return true;
        }

        $isReviewer = $submission->reviewAssignments()
            ->where('reviewer_id', $user->id)
            ->exists();

        if (! $isReviewer) {
            return false;
        }

        return self::visibleFiles($user, $submission)->contains('id', $file->id);
    }

    /**
     * Filter a file collection down to the author-safe set a double-blind
     * reviewer may receive. Used where the viewer is known to be a reviewer
     * under double-blind (e.g. the reviewer timeline and API anonymizer).
     *
     * @param  Collection<int, SubmissionFile>  $files
     * @return Collection<int, SubmissionFile>
     */
    public static function reviewerSafeFiles(Collection $files): Collection
    {
        return $files
            ->filter(fn (SubmissionFile $file) => in_array($file->file_type, self::REVIEWER_SAFE_TYPES, true))
            ->values();
    }

    /**
     * Author of the manuscript, editors/admins of the journal, and platform
     * admins see all files regardless of review model.
     */
    private static function seesEverything(User $user, Submission $submission): bool
    {
        return $submission->author_id === $user->id
            || $user->isPlatformAdmin()
            || $user->hasJournalRole($submission->journal, JournalRole::Editor)
            || $user->hasJournalRole($submission->journal, JournalRole::Admin);
    }
}
