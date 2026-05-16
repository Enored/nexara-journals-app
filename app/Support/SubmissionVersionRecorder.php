<?php

namespace App\Support;

use App\Models\Submission;
use App\Models\SubmissionVersion;
use Illuminate\Support\Carbon;

class SubmissionVersionRecorder
{
    public static function record(Submission $submission, ?Carbon $submittedAt = null): SubmissionVersion
    {
        $version = max(1, (int) ($submission->version ?? 1));

        return SubmissionVersion::query()->updateOrCreate(
            [
                'submission_id' => $submission->id,
                'version' => $version,
            ],
            [
                'title' => $submission->title,
                'abstract' => $submission->abstract,
                'keywords' => $submission->keywords ?? [],
                'submitted_at' => $submittedAt ?? $submission->submitted_at ?? now(),
            ]
        );
    }

    /**
     * Create one snapshot per version number (metadata best-effort for older rounds).
     */
    public static function backfill(Submission $submission): void
    {
        $maxVersion = max(
            (int) $submission->version,
            (int) $submission->files()->max('version') ?: 0,
            1
        );

        $first = SubmissionVersion::query()
            ->where('submission_id', $submission->id)
            ->where('version', 1)
            ->first();

        for ($v = 1; $v <= $maxVersion; $v++) {
            $existing = SubmissionVersion::query()
                ->where('submission_id', $submission->id)
                ->where('version', $v)
                ->first();

            if ($existing) {
                continue;
            }

            $isLatest = $v === (int) $submission->version;
            $fileAt = $submission->files()
                ->where('version', $v)
                ->orderBy('created_at')
                ->value('created_at');

            SubmissionVersion::query()->create([
                'submission_id' => $submission->id,
                'version' => $v,
                'title' => $isLatest ? $submission->title : ($first?->title ?? $submission->title),
                'abstract' => $isLatest ? $submission->abstract : ($first?->abstract ?? $submission->abstract),
                'keywords' => $isLatest ? ($submission->keywords ?? []) : ($first?->keywords ?? $submission->keywords ?? []),
                'submitted_at' => $fileAt
                    ? Carbon::parse($fileAt)
                    : ($v === 1 ? ($submission->submitted_at ?? $submission->created_at) : ($submission->submitted_at ?? now())),
            ]);
        }
    }
}
