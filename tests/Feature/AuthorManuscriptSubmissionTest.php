<?php

namespace Tests\Feature;

use App\Enums\SubmissionFileType;
use App\Enums\SubmissionStatus;
use App\Http\Middleware\RedirectPlatformRoutesToApex;
use App\Models\Journal;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthorManuscriptSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_can_create_manuscript_from_submissions_page(): void
    {
        Storage::fake('local');

        $journal = Journal::query()->create([
            'name' => 'Test Journal',
            'subdomain' => 'test-journal',
            'is_active' => true,
        ]);

        $author = User::factory()->create();

        $file = UploadedFile::fake()->create('paper.pdf', 100, 'application/pdf');

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($author)
            ->post(route('author.submissions.store', absolute: false), [
                'journal_id' => $journal->id,
                'title' => 'A Study of Testing',
                'abstract' => 'We present findings on automated testing in scholarly workflows.',
                'keywords' => 'testing, journals, software',
                'article_type' => 'Research Article',
                'manuscript' => $file,
            ])
            ->assertRedirect(route('author.submissions', absolute: false))
            ->assertSessionHas('status');

        $submission = Submission::query()->where('author_id', $author->id)->first();
        $this->assertNotNull($submission);
        $this->assertSame($journal->id, $submission->journal_id);
        $this->assertSame(SubmissionStatus::Submitted, $submission->status);
        $this->assertSame(['testing', 'journals', 'software'], $submission->keywords);

        $stored = SubmissionFile::query()->where('submission_id', $submission->id)->first();
        $this->assertNotNull($stored);
        $this->assertSame(SubmissionFileType::Manuscript, $stored->file_type);
        Storage::disk('local')->assertExists($stored->storage_path);
    }
}
