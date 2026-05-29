<?php

namespace App\Support;

use App\Enums\EditionStatus;
use App\Enums\SubmissionStatus;
use App\Models\Edition;
use App\Models\Submission;
use App\Models\WorkflowNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class EditionPublisher
{
    public static function publish(Edition $edition): void
    {
        if ($edition->status === EditionStatus::Published) {
            throw ValidationException::withMessages([
                'edition' => 'This issue is already published.',
            ]);
        }

        $slottedCount = $edition->submissions()
            ->where('status', SubmissionStatus::Accepted)
            ->count();

        if ($slottedCount === 0) {
            throw ValidationException::withMessages([
                'edition' => 'Add at least one accepted article to this issue before publishing.',
            ]);
        }

        DB::transaction(function () use ($edition) {
            $edition->update([
                'status' => EditionStatus::Published,
                'published_at' => now(),
            ]);

            $submissions = Submission::query()
                ->where('edition_id', $edition->id)
                ->where('status', SubmissionStatus::Accepted)
                ->get();

            foreach ($submissions as $submission) {
                $submission->update(['status' => SubmissionStatus::Published]);

                WorkflowNotification::query()->create([
                    'user_id' => $submission->author_id,
                    'type' => 'submission_published',
                    'data' => [
                        'submission_id' => $submission->id,
                        'edition_id' => $edition->id,
                    ],
                ]);
            }
        });
    }

    public static function unpublish(Edition $edition): void
    {
        if ($edition->status !== EditionStatus::Published) {
            throw ValidationException::withMessages([
                'edition' => 'This issue is not published.',
            ]);
        }

        DB::transaction(function () use ($edition) {
            Submission::query()
                ->where('edition_id', $edition->id)
                ->where('status', SubmissionStatus::Published)
                ->update(['status' => SubmissionStatus::Accepted]);

            $edition->update([
                'status' => EditionStatus::Draft,
                'published_at' => null,
            ]);
        });
    }

    public static function slotSubmission(Edition $edition, Submission $submission): void
    {
        if ($submission->journal_id !== $edition->journal_id) {
            throw ValidationException::withMessages([
                'submission' => 'This manuscript belongs to a different journal.',
            ]);
        }

        if ($submission->status !== SubmissionStatus::Accepted) {
            throw ValidationException::withMessages([
                'submission' => 'Only accepted manuscripts can be added to an issue.',
            ]);
        }

        if ($submission->edition_id !== null && $submission->edition_id !== $edition->id) {
            throw ValidationException::withMessages([
                'submission' => 'This manuscript is already assigned to another issue.',
            ]);
        }

        DB::transaction(function () use ($edition, $submission) {
            $attributes = ['edition_id' => $edition->id];

            if ($edition->status === EditionStatus::Published) {
                $attributes['status'] = SubmissionStatus::Published;
            }

            $submission->update($attributes);

            if ($edition->status === EditionStatus::Published) {
                WorkflowNotification::query()->create([
                    'user_id' => $submission->author_id,
                    'type' => 'submission_published',
                    'data' => [
                        'submission_id' => $submission->id,
                        'edition_id' => $edition->id,
                    ],
                ]);
            }
        });
    }

    public static function removeSubmission(Edition $edition, Submission $submission): void
    {
        if ($submission->edition_id !== $edition->id) {
            throw ValidationException::withMessages([
                'submission' => 'This manuscript is not in this issue.',
            ]);
        }

        if ($submission->status === SubmissionStatus::Published) {
            throw ValidationException::withMessages([
                'submission' => 'Unpublish the issue before removing a live article.',
            ]);
        }

        $submission->update(['edition_id' => null]);
    }

    public static function delete(Edition $edition): void
    {
        DB::transaction(function () use ($edition) {
            if ($edition->status === EditionStatus::Published) {
                Submission::query()
                    ->where('edition_id', $edition->id)
                    ->where('status', SubmissionStatus::Published)
                    ->update(['status' => SubmissionStatus::Accepted]);
            }

            Submission::query()
                ->where('edition_id', $edition->id)
                ->update(['edition_id' => null]);

            $edition->delete();
        });
    }
}
