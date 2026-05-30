<?php

namespace Tests\Feature;

use App\Enums\JournalRole;
use App\Enums\ReviewAssignmentStatus;
use App\Enums\ReviewModel;
use App\Enums\SubmissionStatus;
use App\Models\Journal;
use App\Models\JournalUserRole;
use App\Models\ReviewAssignment;
use App\Models\Submission;
use App\Models\User;
use App\Support\SubmissionAuthorAnonymizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionAnonymizerTest extends TestCase
{
    use RefreshDatabase;

    private function makeSubmission(ReviewModel $model, User $author): Submission
    {
        $journal = Journal::query()->create([
            'name' => 'Review Journal',
            'subdomain' => 'review-journal',
            'is_active' => true,
            'review_model' => $model->value,
        ]);

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

        ReviewAssignment::query()->create([
            'submission_id' => $submission->id,
            'round_version' => 1,
            'reviewer_id' => User::factory()->create()->id,
            'editor_id' => User::factory()->create()->id,
            'status' => ReviewAssignmentStatus::Assigned,
            'deadline' => now()->addWeek(),
            'invited_at' => now(),
        ]);

        return $submission;
    }

    private function loadForViewer(Submission $submission): Submission
    {
        return $submission->fresh([
            'journal',
            'author',
            'reviewAssignments.reviewer',
            'reviewAssignments.editor',
            'reviews.reviewer',
            'editorialDecisions.recorder',
        ]);
    }

    public function test_double_blind_hides_author_from_reviewer(): void
    {
        $author = User::factory()->create();
        $submission = $this->makeSubmission(ReviewModel::DoubleBlind, $author);
        $reviewer = User::factory()->create();

        $loaded = $this->loadForViewer($submission);
        SubmissionAuthorAnonymizer::forViewer($reviewer, $loaded);

        $array = $loaded->toArray();
        $this->assertArrayNotHasKey('author', $array);
        $this->assertArrayNotHasKey('author_id', $array);
    }

    public function test_single_blind_shows_author_to_reviewer(): void
    {
        $author = User::factory()->create();
        $submission = $this->makeSubmission(ReviewModel::SingleBlind, $author);
        $reviewer = User::factory()->create();

        $loaded = $this->loadForViewer($submission);
        SubmissionAuthorAnonymizer::forViewer($reviewer, $loaded);

        $this->assertTrue($loaded->relationLoaded('author'));
        $this->assertSame($author->id, $loaded->author_id);
    }

    public function test_open_review_shows_author_to_reviewer(): void
    {
        $author = User::factory()->create();
        $submission = $this->makeSubmission(ReviewModel::OpenReview, $author);
        $reviewer = User::factory()->create();

        $loaded = $this->loadForViewer($submission);
        SubmissionAuthorAnonymizer::forViewer($reviewer, $loaded);

        $this->assertTrue($loaded->relationLoaded('author'));
        $this->assertSame($author->id, $loaded->author_id);
    }

    public function test_single_blind_hides_reviewer_identity_from_author(): void
    {
        $author = User::factory()->create();
        $submission = $this->makeSubmission(ReviewModel::SingleBlind, $author);

        $loaded = $this->loadForViewer($submission);
        SubmissionAuthorAnonymizer::forViewer($author, $loaded);

        $assignment = $loaded->reviewAssignments->first();
        $this->assertFalse($assignment->relationLoaded('reviewer'));
        $this->assertSame('Reviewer 1', $assignment->reviewer_label);
    }

    public function test_open_review_shows_reviewer_identity_to_author(): void
    {
        $author = User::factory()->create();
        $submission = $this->makeSubmission(ReviewModel::OpenReview, $author);

        $loaded = $this->loadForViewer($submission);
        SubmissionAuthorAnonymizer::forViewer($author, $loaded);

        $assignment = $loaded->reviewAssignments->first();
        $this->assertTrue($assignment->relationLoaded('reviewer'));
    }

    public function test_editor_sees_full_identities_under_double_blind(): void
    {
        $author = User::factory()->create();
        $submission = $this->makeSubmission(ReviewModel::DoubleBlind, $author);

        $editor = User::factory()->create();
        JournalUserRole::query()->create([
            'user_id' => $editor->id,
            'journal_id' => $submission->journal_id,
            'role' => JournalRole::Editor,
        ]);

        $loaded = $this->loadForViewer($submission);
        SubmissionAuthorAnonymizer::forViewer($editor, $loaded);

        $this->assertTrue($loaded->relationLoaded('author'));
        $this->assertSame($author->id, $loaded->author_id);
    }

    public function test_platform_admin_sees_full_identities_under_double_blind(): void
    {
        $author = User::factory()->create();
        $submission = $this->makeSubmission(ReviewModel::DoubleBlind, $author);
        $admin = User::factory()->platformAdmin()->create();

        $loaded = $this->loadForViewer($submission);
        SubmissionAuthorAnonymizer::forViewer($admin, $loaded);

        $this->assertTrue($loaded->relationLoaded('author'));
        $this->assertSame($author->id, $loaded->author_id);
    }

    public function test_review_model_defaults_to_single_blind_when_journal_unset(): void
    {
        $journal = Journal::query()->create([
            'name' => 'Unset Journal',
            'subdomain' => 'unset-journal',
            'is_active' => true,
        ]);

        $this->assertSame(ReviewModel::SingleBlind, $journal->fresh()->review_model);
    }
}
