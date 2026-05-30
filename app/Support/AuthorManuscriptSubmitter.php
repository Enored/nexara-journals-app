<?php

namespace App\Support;

use App\Enums\JournalRole;
use App\Enums\SubmissionFileType;
use App\Enums\SubmissionStatus;
use App\Models\Journal;
use App\Models\JournalUserRole;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\User;
use App\Models\WorkflowNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AuthorManuscriptSubmitter
{
    /**
     * @param  array{
     *     journal_id: string,
     *     title: string,
     *     abstract: string,
     *     keywords: string,
     *     article_type: string,
     *     manuscript: UploadedFile,
     *     blinded_manuscript?: UploadedFile|null
     * }  $data
     */
    public static function submit(array $data, User $author): Submission
    {
        $journal = Journal::query()
            ->where('id', $data['journal_id'])
            ->where('is_active', true)
            ->first();

        if (! $journal) {
            throw ValidationException::withMessages([
                'journal_id' => 'Select an active journal.',
            ]);
        }

        $keywords = array_values(array_filter(array_map('trim', preg_split('/[,;]+/', $data['keywords']))));

        if ($keywords === []) {
            throw ValidationException::withMessages([
                'keywords' => 'Enter at least one keyword.',
            ]);
        }

        $file = $data['manuscript'];
        $blinded = $data['blinded_manuscript'] ?? null;

        if ($journal->review_model->hidesAuthorFromReviewer() && ! $blinded instanceof UploadedFile) {
            throw ValidationException::withMessages([
                'blinded_manuscript' => 'This journal uses double-blind review. Upload an anonymized manuscript with author names, affiliations, and acknowledgements removed.',
            ]);
        }

        return DB::transaction(function () use ($journal, $author, $data, $keywords, $file, $blinded) {
            $submission = Submission::query()->create([
                'journal_id' => $journal->id,
                'author_id' => $author->id,
                'title' => $data['title'],
                'abstract' => $data['abstract'],
                'keywords' => $keywords,
                'article_type' => $data['article_type'],
                'status' => SubmissionStatus::Screening,
                'version' => 1,
                'submitted_at' => now(),
            ]);

            self::storeFile($submission, $file, SubmissionFileType::Manuscript, 1, $author->id);

            if ($blinded instanceof UploadedFile) {
                self::storeFile($submission, $blinded, SubmissionFileType::BlindedManuscript, 1, $author->id);
            }

            SubmissionVersionRecorder::record($submission->fresh());

            $editorIds = JournalUserRole::query()
                ->where('journal_id', $journal->id)
                ->whereIn('role', [JournalRole::Editor, JournalRole::Admin])
                ->pluck('user_id')
                ->unique();

            foreach ($editorIds as $userId) {
                WorkflowNotification::query()->create([
                    'user_id' => $userId,
                    'type' => 'submission_received',
                    'data' => [
                        'submission_id' => $submission->id,
                        'journal_id' => $journal->id,
                    ],
                ]);
            }

            return $submission->fresh(['journal']);
        });
    }

    private static function storeFile(
        Submission $submission,
        UploadedFile $file,
        SubmissionFileType $type,
        int $version,
        string $uploadedBy,
    ): void {
        $path = $file->store('submissions/'.$submission->id, SubmissionFile::DISK);

        SubmissionFile::query()->create([
            'submission_id' => $submission->id,
            'file_type' => $type,
            'original_name' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getClientMimeType() ?? 'application/octet-stream',
            'file_size' => $file->getSize(),
            'version' => $version,
            'uploaded_by' => $uploadedBy,
        ]);
    }
}
