<?php

namespace Tests\Feature;

use App\Enums\ReviewModel;
use App\Http\Middleware\RedirectPlatformRoutesToApex;
use App\Models\Journal;
use App\Models\User;
use App\Support\JournalsDirectoryPayload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return [
            'name' => 'Journal of Tests',
            'subdomain' => 'jot',
            'abbreviation' => 'J. Tests',
            'e_issn' => '2845-1739',
            'p_issn' => '2845-1720',
            'review_model' => ReviewModel::DoubleBlind->value,
            'frequency' => 'Quarterly',
            'license_type' => 'CC BY 4.0',
            'contact_email' => 'support@jot.test',
            'cover_image_url' => 'https://cdn.test/cover.jpg',
            'excerpt' => 'A short summary.',
            'description' => 'A longer description.',
            ...$overrides,
        ];
    }

    public function test_admin_can_create_journal_with_profile_fields(): void
    {
        $admin = User::factory()->platformAdmin()->create();

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($admin)
            ->post(route('admin.journals.store', absolute: false), $this->validPayload())
            ->assertRedirect(route('admin.journals.index', absolute: false));

        $journal = Journal::query()->where('subdomain', 'jot')->firstOrFail();
        $this->assertSame('J. Tests', $journal->abbreviation);
        $this->assertSame('2845-1739', $journal->e_issn);
        $this->assertSame('2845-1720', $journal->p_issn);
        $this->assertSame(ReviewModel::DoubleBlind, $journal->review_model);
        $this->assertSame('CC BY 4.0', $journal->license_type);
        $this->assertSame('support@jot.test', $journal->contact_email);
        $this->assertSame('https://cdn.test/cover.jpg', $journal->cover_image_url);
    }

    public function test_review_model_defaults_to_single_blind_when_omitted(): void
    {
        $admin = User::factory()->platformAdmin()->create();

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($admin)
            ->post(route('admin.journals.store', absolute: false), $this->validPayload(['review_model' => '']))
            ->assertRedirect(route('admin.journals.index', absolute: false));

        $journal = Journal::query()->where('subdomain', 'jot')->firstOrFail();
        $this->assertSame(ReviewModel::SingleBlind, $journal->review_model);
    }

    public function test_invalid_issn_email_url_and_review_model_are_rejected(): void
    {
        $admin = User::factory()->platformAdmin()->create();

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($admin)
            ->post(route('admin.journals.store', absolute: false), $this->validPayload([
                'e_issn' => '12-34',
                'contact_email' => 'not-an-email',
                'cover_image_url' => 'notaurl',
                'review_model' => 'triple_blind',
            ]))
            ->assertSessionHasErrors(['e_issn', 'contact_email', 'cover_image_url', 'review_model']);

        $this->assertDatabaseMissing('journals', ['subdomain' => 'jot']);
    }

    public function test_directory_payload_prefers_stored_abbreviation_and_excerpt(): void
    {
        $journal = Journal::query()->create([
            'name' => 'Journal of Cognitive Neuroscience',
            'subdomain' => 'jcn',
            'is_active' => true,
            'abbreviation' => 'JCN Official',
            'excerpt' => 'Stored excerpt wins.',
            'description' => 'Description fallback.',
            'cover_image_url' => 'https://cdn.test/jcn.jpg',
        ]);

        $payload = JournalsDirectoryPayload::mapJournal($journal->fresh());

        $this->assertSame('JCN Official', $payload['abbr']);
        $this->assertStringStartsWith('Stored excerpt wins.', $payload['field']);
        $this->assertSame('https://cdn.test/jcn.jpg', $payload['cover']);
    }

    public function test_directory_payload_falls_back_to_derived_abbreviation(): void
    {
        $journal = Journal::query()->create([
            'name' => 'Journal of Cognitive Neuroscience',
            'subdomain' => 'jcn2',
            'is_active' => true,
        ]);

        $payload = JournalsDirectoryPayload::mapJournal($journal->fresh());

        $this->assertSame('JOC', $payload['abbr']);
        $this->assertNull($payload['cover']);
    }
}
