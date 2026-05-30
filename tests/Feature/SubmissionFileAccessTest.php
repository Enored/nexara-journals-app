<?php

namespace Tests\Feature;

use App\Enums\JournalRole;
use App\Enums\ReviewAssignmentStatus;
use App\Enums\ReviewModel;
use App\Enums\SubmissionFileType;
use App\Enums\SubmissionStatus;
use App\Http\Middleware\RedirectPlatformRoutesToApex;
use App\Models\Journal;
use App\Models\JournalUserRole;
use App\Models\ReviewAssignment;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\User;
use App\Support\SubmissionFileAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubmissionFileAccessTest extends TestCase
{
    use RefreshDatabase;

    private function makeSubmission(ReviewModel $model, User $author): Submission
    {
        $journal = Journal::query()->create([
            'name' => 'Access Journal',
            'subdomain' => 'access-journal',
            'is_active' => true,
            'review_model' => $model->value,
        ]);

        return Submission::query()->create([
            'journal_id' => $journal->id,
            'author_id' => $author->id,
            'title' => 'Manuscript',
            'abstract' => 'Abstract.',
            'keywords' => ['alpha'],
            'article_type' => 'Research Article',
            'status' => SubmissionStatus::UnderReview,
            'version' => 1,
            'submitted_at' => now(),
        ]);
    }

    private function addFile(Submission $submission, SubmissionFileType $type, User $uploader): SubmissionFile
    {
        $path = UploadedFile::fake()
            ->create('doc.pdf', 10, 'application/pdf')
            ->store('submissions/'.$submission->id, SubmissionFile::DISK);

        return SubmissionFile::query()->create([
            'submission_id' => $submission->id,
            'file_type' => $type,
            'original_name' => $type->value.'.pdf',
            'storage_path' => $path,
            'mime_type' => 'application/pdf',
            'file_size' => 10240,
            'version' => 1,
            'uploaded_by' => $uploader->id,
        ]);
    }

    private function assignReviewer(Submission $submission, User $reviewer): void
    {
        ReviewAssignment::query()->create([
            'submission_id' => $submission->id,
            'round_version' => 1,
            'reviewer_id' => $reviewer->id,
            'editor_id' => User::factory()->create()->id,
            'status' => ReviewAssignmentStatus::Assigned,
            'deadline' => now()->addWeek(),
            'invited_at' => now(),
        ]);
    }

    public function test_double_blind_reviewer_sees_only_blinded_and_supplementary(): void
    {
        Storage::fake('local');
        $author = User::factory()->create();
        $submission = $this->makeSubmission(ReviewModel::DoubleBlind, $author);
        $this->addFile($submission, SubmissionFileType::Manuscript, $author);
        $this->addFile($submission, SubmissionFileType::BlindedManuscript, $author);
        $this->addFile($submission, SubmissionFileType::Supplementary, $author);

        $reviewer = User::factory()->create();
        $this->assignReviewer($submission, $reviewer);

        $visible = SubmissionFileAccess::visibleFiles($reviewer, $submission->fresh(['journal', 'files']))
            ->pluck('file_type')
            ->all();

        $this->assertEqualsCanonicalizing(
            [SubmissionFileType::BlindedManuscript, SubmissionFileType::Supplementary],
            $visible
        );
    }

    public function test_single_blind_reviewer_sees_all_files(): void
    {
        Storage::fake('local');
        $author = User::factory()->create();
        $submission = $this->makeSubmission(ReviewModel::SingleBlind, $author);
        $this->addFile($submission, SubmissionFileType::Manuscript, $author);
        $this->addFile($submission, SubmissionFileType::Supplementary, $author);

        $reviewer = User::factory()->create();
        $this->assignReviewer($submission, $reviewer);

        $this->assertCount(2, SubmissionFileAccess::visibleFiles($reviewer, $submission->fresh(['journal', 'files'])));
    }

    public function test_author_and_editor_see_all_files_under_double_blind(): void
    {
        Storage::fake('local');
        $author = User::factory()->create();
        $submission = $this->makeSubmission(ReviewModel::DoubleBlind, $author);
        $this->addFile($submission, SubmissionFileType::Manuscript, $author);
        $this->addFile($submission, SubmissionFileType::BlindedManuscript, $author);

        $editor = User::factory()->create();
        JournalUserRole::query()->create([
            'user_id' => $editor->id,
            'journal_id' => $submission->journal_id,
            'role' => JournalRole::Editor,
        ]);

        $this->assertCount(2, SubmissionFileAccess::visibleFiles($author, $submission->fresh(['journal', 'files'])));
        $this->assertCount(2, SubmissionFileAccess::visibleFiles($editor, $submission->fresh(['journal', 'files'])));
    }

    public function test_reviewer_cannot_download_full_manuscript_under_double_blind(): void
    {
        Storage::fake('local');
        $author = User::factory()->create();
        $submission = $this->makeSubmission(ReviewModel::DoubleBlind, $author);
        $manuscript = $this->addFile($submission, SubmissionFileType::Manuscript, $author);

        $reviewer = User::factory()->create();
        $this->assignReviewer($submission, $reviewer);

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($reviewer)
            ->get(route('submission-files.download', $manuscript, absolute: false))
            ->assertForbidden();
    }

    public function test_reviewer_can_download_blinded_manuscript_under_double_blind(): void
    {
        Storage::fake('local');
        $author = User::factory()->create();
        $submission = $this->makeSubmission(ReviewModel::DoubleBlind, $author);
        $blinded = $this->addFile($submission, SubmissionFileType::BlindedManuscript, $author);

        $reviewer = User::factory()->create();
        $this->assignReviewer($submission, $reviewer);

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($reviewer)
            ->get(route('submission-files.download', $blinded, absolute: false))
            ->assertOk();
    }

    public function test_editor_can_download_full_manuscript(): void
    {
        Storage::fake('local');
        $author = User::factory()->create();
        $submission = $this->makeSubmission(ReviewModel::DoubleBlind, $author);
        $manuscript = $this->addFile($submission, SubmissionFileType::Manuscript, $author);

        $editor = User::factory()->create();
        JournalUserRole::query()->create([
            'user_id' => $editor->id,
            'journal_id' => $submission->journal_id,
            'role' => JournalRole::Editor,
        ]);

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($editor)
            ->get(route('submission-files.download', $manuscript, absolute: false))
            ->assertOk();
    }

    public function test_unrelated_user_cannot_download_any_file(): void
    {
        Storage::fake('local');
        $author = User::factory()->create();
        $submission = $this->makeSubmission(ReviewModel::SingleBlind, $author);
        $manuscript = $this->addFile($submission, SubmissionFileType::Manuscript, $author);

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs(User::factory()->create())
            ->get(route('submission-files.download', $manuscript, absolute: false))
            ->assertForbidden();
    }
}
