<?php

namespace Tests\Feature;

use App\Enums\JournalRole;
use App\Enums\ReviewModel;
use App\Enums\SubmissionFileType;
use App\Enums\SubmissionStatus;
use App\Http\Middleware\RedirectPlatformRoutesToApex;
use App\Models\Journal;
use App\Models\JournalUserRole;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\User;
use App\Models\WorkflowNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlindedSubmissionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function journal(ReviewModel $model): Journal
    {
        return Journal::query()->create([
            'name' => 'Blind Journal',
            'subdomain' => 'blind-journal',
            'is_active' => true,
            'review_model' => $model->value,
        ]);
    }

    private function basePayload(Journal $journal): array
    {
        return [
            'journal_id' => $journal->id,
            'title' => 'A Study of Testing',
            'abstract' => 'We present findings on automated testing in scholarly workflows.',
            'keywords' => 'testing, journals',
            'article_type' => 'Research Article',
            'manuscript' => UploadedFile::fake()->create('paper.pdf', 100, 'application/pdf'),
        ];
    }

    public function test_double_blind_submission_requires_blinded_file(): void
    {
        Storage::fake('local');
        $journal = $this->journal(ReviewModel::DoubleBlind);
        $author = User::factory()->create();

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($author)
            ->post(route('author.submissions.store', absolute: false), $this->basePayload($journal))
            ->assertSessionHasErrors('blinded_manuscript');

        $this->assertSame(0, Submission::query()->count());
    }

    public function test_double_blind_submission_succeeds_with_blinded_file(): void
    {
        Storage::fake('local');
        $journal = $this->journal(ReviewModel::DoubleBlind);
        $author = User::factory()->create();

        $payload = $this->basePayload($journal);
        $payload['blinded_manuscript'] = UploadedFile::fake()->create('blinded.pdf', 100, 'application/pdf');

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($author)
            ->post(route('author.submissions.store', absolute: false), $payload)
            ->assertSessionHasNoErrors();

        $submission = Submission::query()->where('author_id', $author->id)->firstOrFail();
        $types = SubmissionFile::query()->where('submission_id', $submission->id)->pluck('file_type')->all();

        $this->assertEqualsCanonicalizing(
            [SubmissionFileType::Manuscript, SubmissionFileType::BlindedManuscript],
            $types
        );
    }

    public function test_single_blind_submission_does_not_require_blinded_file(): void
    {
        Storage::fake('local');
        $journal = $this->journal(ReviewModel::SingleBlind);
        $author = User::factory()->create();

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($author)
            ->post(route('author.submissions.store', absolute: false), $this->basePayload($journal))
            ->assertSessionHasNoErrors();

        $this->assertSame(1, Submission::query()->where('author_id', $author->id)->count());
    }

    public function test_editor_can_return_submission_for_screening_before_review(): void
    {
        $journal = $this->journal(ReviewModel::DoubleBlind);
        $author = User::factory()->create();
        $submission = Submission::query()->create([
            'journal_id' => $journal->id,
            'author_id' => $author->id,
            'title' => 'Manuscript',
            'abstract' => 'Abstract.',
            'keywords' => ['alpha'],
            'article_type' => 'Research Article',
            'status' => SubmissionStatus::Screening,
            'version' => 1,
            'submitted_at' => now(),
        ]);

        $editor = User::factory()->create();
        JournalUserRole::query()->create([
            'user_id' => $editor->id,
            'journal_id' => $journal->id,
            'role' => JournalRole::Editor,
        ]);

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($editor)
            ->post(route('editor.submissions.return-to-author', $submission, absolute: false), [
                'note' => 'Please remove your name from the anonymized file before review.',
            ])
            ->assertSessionHas('status');

        $submission->refresh();
        $this->assertSame(SubmissionStatus::RevisionRequested, $submission->status);
        $this->assertStringContainsString('anonymized file', (string) $submission->decision_letter);

        $this->assertSame(1, WorkflowNotification::query()
            ->where('user_id', $author->id)
            ->where('type', 'screening_returned')
            ->count());
    }

    public function test_editor_cannot_screen_return_after_review_started(): void
    {
        $journal = $this->journal(ReviewModel::DoubleBlind);
        $author = User::factory()->create();
        $submission = Submission::query()->create([
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

        $editor = User::factory()->create();
        JournalUserRole::query()->create([
            'user_id' => $editor->id,
            'journal_id' => $journal->id,
            'role' => JournalRole::Editor,
        ]);

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($editor)
            ->post(route('editor.submissions.return-to-author', $submission, absolute: false), [
                'note' => 'Too late.',
            ])
            ->assertForbidden();
    }
}
