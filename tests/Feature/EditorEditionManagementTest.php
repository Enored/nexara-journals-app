<?php

namespace Tests\Feature;

use App\Enums\EditionStatus;
use App\Enums\JournalRole;
use App\Enums\SubmissionStatus;
use App\Http\Middleware\RedirectPlatformRoutesToApex;
use App\Models\Edition;
use App\Models\Journal;
use App\Models\JournalUserRole;
use App\Models\Submission;
use App\Models\User;
use App\Models\Volume;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditorEditionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_editor_can_access_issues_and_volumes_for_their_journal(): void
    {
        [$journal, $editor] = $this->editorWithJournal();

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($editor)
            ->get(route('editor.journals.index', absolute: false))
            ->assertOk()
            ->assertSee($journal->name);

        $this->actingAs($editor)
            ->get(route('journal.editions.index', [$journal], false))
            ->assertOk()
            ->assertSee('Issues & volumes');
    }

    public function test_non_editor_cannot_access_edition_management(): void
    {
        $journal = Journal::query()->create([
            'name' => 'Other Journal',
            'subdomain' => 'other-journal',
        ]);
        $user = User::factory()->create();

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($user)
            ->get(route('journal.editions.index', [$journal], false))
            ->assertForbidden();
    }

    public function test_editor_can_create_volume_issue_assign_and_publish(): void
    {
        [$journal, $editor] = $this->editorWithJournal();

        $author = User::factory()->create();
        $submission = Submission::query()->create([
            'journal_id' => $journal->id,
            'author_id' => $author->id,
            'title' => 'Accepted manuscript',
            'abstract' => 'Abstract text.',
            'keywords' => ['science'],
            'article_type' => 'research',
            'status' => SubmissionStatus::Accepted,
            'submitted_at' => now(),
        ]);

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($editor);

        $this->post(route('journal.volumes.store', [$journal], false), [
            'number' => 1,
            'title' => 'Volume One',
        ])->assertRedirect();

        $volume = Volume::query()->where('journal_id', $journal->id)->first();
        $this->assertNotNull($volume);

        $this->post(route('journal.editions.store', [$journal], false), [
            'volume_id' => $volume->id,
            'issue' => 1,
            'title' => 'Spring issue',
        ])->assertRedirect();

        $edition = Edition::query()->where('journal_id', $journal->id)->first();
        $this->assertNotNull($edition);
        $this->assertSame(EditionStatus::Draft, $edition->status);

        $this->post(route('journal.editions.articles.assign', [$journal, $edition], false), [
            'submission_id' => $submission->id,
        ])->assertRedirect();

        $this->assertSame($edition->id, $submission->fresh()->edition_id);

        $this->post(route('journal.editions.publish', [$journal, $edition], false))
            ->assertRedirect();

        $edition->refresh();
        $submission->refresh();
        $this->assertSame(EditionStatus::Published, $edition->status);
        $this->assertSame(SubmissionStatus::Published, $submission->status);

        $this->post(route('journal.editions.unpublish', [$journal, $edition], false))
            ->assertRedirect();

        $edition->refresh();
        $submission->refresh();
        $this->assertSame(EditionStatus::Draft, $edition->status);
        $this->assertSame(SubmissionStatus::Accepted, $submission->status);
    }

    /**
     * @return array{0: Journal, 1: User}
     */
    private function editorWithJournal(): array
    {
        $journal = Journal::query()->create([
            'name' => 'Editor Journal',
            'subdomain' => 'editor-journal',
        ]);
        $editor = User::factory()->create();

        JournalUserRole::query()->create([
            'journal_id' => $journal->id,
            'user_id' => $editor->id,
            'role' => JournalRole::Editor,
        ]);

        return [$journal, $editor];
    }
}
