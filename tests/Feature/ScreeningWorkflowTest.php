<?php

namespace Tests\Feature;

use App\Enums\JournalRole;
use App\Enums\ReviewModel;
use App\Enums\SubmissionStatus;
use App\Http\Middleware\RedirectPlatformRoutesToApex;
use App\Models\Journal;
use App\Models\JournalUserRole;
use App\Models\Submission;
use App\Models\SubmissionEditorialDecision;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScreeningWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function screeningSubmission(User $author): Submission
    {
        $journal = Journal::query()->create([
            'name' => 'Screen Journal',
            'subdomain' => 'screen-journal',
            'is_active' => true,
            'review_model' => ReviewModel::SingleBlind->value,
        ]);

        return Submission::query()->create([
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
    }

    private function editorFor(Submission $submission): User
    {
        $editor = User::factory()->create();
        JournalUserRole::query()->create([
            'user_id' => $editor->id,
            'journal_id' => $submission->journal_id,
            'role' => JournalRole::Editor,
        ]);

        return $editor;
    }

    public function test_send_for_review_clears_screening(): void
    {
        $author = User::factory()->create();
        $submission = $this->screeningSubmission($author);
        $editor = $this->editorFor($submission);

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($editor)
            ->post(route('editor.submissions.send-for-review', $submission, absolute: false))
            ->assertSessionHas('status');

        $this->assertSame(SubmissionStatus::Submitted, $submission->fresh()->status);
    }

    public function test_desk_reject_rejects_and_records_decision(): void
    {
        $author = User::factory()->create();
        $submission = $this->screeningSubmission($author);
        $editor = $this->editorFor($submission);

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($editor)
            ->post(route('editor.submissions.desk-reject', $submission, absolute: false), [
                'note' => 'Out of scope for this journal.',
            ])
            ->assertSessionHas('status');

        $submission->refresh();
        $this->assertSame(SubmissionStatus::Rejected, $submission->status);
        $this->assertSame(1, SubmissionEditorialDecision::query()
            ->where('submission_id', $submission->id)
            ->where('decision', 'reject')
            ->count());
    }

    public function test_reviewer_cannot_be_assigned_during_screening(): void
    {
        $author = User::factory()->create();
        $submission = $this->screeningSubmission($author);
        $editor = $this->editorFor($submission);
        $reviewer = User::factory()->create();

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($editor)
            ->post(route('editor.submissions.assign-reviewer', $submission, absolute: false), [
                'reviewer_id' => $reviewer->id,
                'deadline' => now()->addWeek()->toDateString(),
            ])
            ->assertForbidden();

        $this->assertSame(SubmissionStatus::Screening, $submission->fresh()->status);
    }

    public function test_send_for_review_blocked_once_not_in_screening(): void
    {
        $author = User::factory()->create();
        $submission = $this->screeningSubmission($author);
        $submission->update(['status' => SubmissionStatus::UnderReview]);
        $editor = $this->editorFor($submission);

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($editor)
            ->post(route('editor.submissions.send-for-review', $submission, absolute: false))
            ->assertForbidden();
    }
}
