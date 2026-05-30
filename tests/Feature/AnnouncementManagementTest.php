<?php

namespace Tests\Feature;

use App\Enums\AnnouncementCategory;
use App\Enums\AnnouncementScope;
use App\Enums\AnnouncementStatus;
use App\Enums\AnnouncementType;
use App\Http\Middleware\RedirectPlatformRoutesToApex;
use App\Models\Announcement;
use App\Models\Journal;
use App\Models\User;
use App\Support\AnnouncementRules;
use App\Support\JournalAnnouncementsPayload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class AnnouncementManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return [
            'scope' => AnnouncementScope::Global->value,
            'journal_id' => '',
            'category' => AnnouncementCategory::Editorial->value,
            'type' => AnnouncementType::Info->value,
            'status' => AnnouncementStatus::Published->value,
            'title' => 'Indexing milestone reached',
            'body' => "We are pleased to share this update.\n\nThank you for your continued support.",
            'url' => '',
            'expires_at' => '',
            ...$overrides,
        ];
    }

    private function asAdmin(): User
    {
        return User::factory()->platformAdmin()->create();
    }

    public function test_admin_can_create_global_announcement(): void
    {
        $admin = $this->asAdmin();

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($admin)
            ->post(route('admin.announcements.store', absolute: false), $this->validPayload())
            ->assertRedirect(route('admin.announcements.index', absolute: false));

        $announcement = Announcement::query()->firstOrFail();
        $this->assertSame(AnnouncementScope::Global, $announcement->scope);
        $this->assertNull($announcement->journal_id);
        $this->assertSame('Indexing milestone reached', $announcement->title);
        $this->assertSame(AnnouncementStatus::Published, $announcement->status);
    }

    public function test_admin_can_create_per_journal_announcement(): void
    {
        $journal = Journal::query()->create([
            'name' => 'Per Journal Test',
            'subdomain' => 'pjt',
            'is_active' => true,
        ]);
        $admin = $this->asAdmin();

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($admin)
            ->post(route('admin.announcements.store', absolute: false), $this->validPayload([
                'scope' => AnnouncementScope::PerJournal->value,
                'journal_id' => $journal->id,
                'category' => AnnouncementCategory::CallForPapers->value,
            ]))
            ->assertRedirect(route('admin.announcements.index', absolute: false));

        $announcement = Announcement::query()->firstOrFail();
        $this->assertSame(AnnouncementScope::PerJournal, $announcement->scope);
        $this->assertSame($journal->id, $announcement->journal_id);
    }

    public function test_per_journal_scope_requires_journal_id(): void
    {
        $admin = $this->asAdmin();

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($admin)
            ->post(route('admin.announcements.store', absolute: false), $this->validPayload([
                'scope' => AnnouncementScope::PerJournal->value,
                'journal_id' => '',
            ]))
            ->assertSessionHasErrors('journal_id');
    }

    public function test_global_scope_rejects_journal_id(): void
    {
        $journal = Journal::query()->create([
            'name' => 'Per Journal Test',
            'subdomain' => 'pjt',
            'is_active' => true,
        ]);
        $admin = $this->asAdmin();

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($admin)
            ->post(route('admin.announcements.store', absolute: false), $this->validPayload([
                'journal_id' => $journal->id,
            ]))
            ->assertSessionHasErrors('journal_id');
    }

    public function test_admin_can_update_and_delete_announcement(): void
    {
        $announcement = Announcement::query()->create([
            'scope' => AnnouncementScope::Global,
            'category' => AnnouncementCategory::Policy,
            'type' => AnnouncementType::Warning,
            'status' => AnnouncementStatus::Draft,
            'title' => 'Old title',
            'body' => 'Old body',
        ]);
        $admin = $this->asAdmin();

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($admin)
            ->put(route('admin.announcements.update', $announcement, absolute: false), $this->validPayload([
                'title' => 'Updated title',
                'status' => AnnouncementStatus::Archived->value,
            ]))
            ->assertRedirect(route('admin.announcements.index', absolute: false));

        $announcement->refresh();
        $this->assertSame('Updated title', $announcement->title);
        $this->assertSame(AnnouncementStatus::Archived, $announcement->status);

        $this->actingAs($admin)
            ->delete(route('admin.announcements.destroy', $announcement, absolute: false))
            ->assertRedirect(route('admin.announcements.index', absolute: false));

        $this->assertDatabaseMissing('announcements', ['id' => $announcement->id]);
    }

    public function test_non_admin_cannot_manage_announcements(): void
    {
        $user = User::factory()->create();

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($user)
            ->get(route('admin.announcements.index', absolute: false))
            ->assertForbidden();
    }

    public function test_public_journal_home_only_shows_published_non_expired_announcements(): void
    {
        $journal = Journal::query()->create([
            'name' => 'Journal of Cognition',
            'subdomain' => 'cognition',
            'is_active' => true,
        ]);

        Announcement::query()->create([
            'scope' => AnnouncementScope::Global,
            'category' => AnnouncementCategory::Milestone,
            'type' => AnnouncementType::Success,
            'status' => AnnouncementStatus::Published,
            'title' => 'Global live',
            'body' => 'Visible globally.',
        ]);

        Announcement::query()->create([
            'journal_id' => $journal->id,
            'scope' => AnnouncementScope::PerJournal,
            'category' => AnnouncementCategory::Editorial,
            'type' => AnnouncementType::Info,
            'status' => AnnouncementStatus::Published,
            'title' => 'Journal live',
            'body' => 'Visible on this journal.',
        ]);

        Announcement::query()->create([
            'journal_id' => $journal->id,
            'scope' => AnnouncementScope::PerJournal,
            'category' => AnnouncementCategory::Policy,
            'type' => AnnouncementType::Warning,
            'status' => AnnouncementStatus::Draft,
            'title' => 'Draft hidden',
            'body' => 'Not visible.',
        ]);

        Announcement::query()->create([
            'scope' => AnnouncementScope::Global,
            'category' => AnnouncementCategory::SystemUpdate,
            'type' => AnnouncementType::Info,
            'status' => AnnouncementStatus::Published,
            'title' => 'Expired hidden',
            'body' => 'Past expiry.',
            'expires_at' => now()->subDay(),
        ]);

        $otherJournal = Journal::query()->create([
            'name' => 'Other Journal',
            'subdomain' => 'other',
            'is_active' => true,
        ]);
        Announcement::query()->create([
            'journal_id' => $otherJournal->id,
            'scope' => AnnouncementScope::PerJournal,
            'category' => AnnouncementCategory::Event,
            'type' => AnnouncementType::Info,
            'status' => AnnouncementStatus::Published,
            'title' => 'Other journal only',
            'body' => 'Not on cognition.',
        ]);

        $base = (string) config('journal.base_domain');

        $this->get("http://cognition.{$base}/")
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('announcements', 2)
                ->where('announcements', fn ($items) => collect($items->toArray())
                    ->pluck('title')
                    ->sort()
                    ->values()
                    ->all() === ['Global live', 'Journal live']
                )
            );

        $visible = JournalAnnouncementsPayload::forJournal($journal);
        $this->assertCount(2, $visible);
        $titles = collect($visible)->pluck('title')->sort()->values()->all();
        $this->assertSame(['Global live', 'Journal live'], $titles);
        $global = collect($visible)->firstWhere('title', 'Global live');
        $this->assertSame('Milestone', $global['category']);
        $this->assertSame('success', $global['type']);
    }

    public function test_expired_announcement_from_datetime_local_input_is_hidden(): void
    {
        $this->travelTo(Carbon::parse('2026-05-30 16:00:00', config('app.timezone')));

        $journal = Journal::query()->create([
            'name' => 'Expiry Journal',
            'subdomain' => 'expiry',
            'is_active' => true,
        ]);

        $expiresAt = AnnouncementRules::parseExpiresAt('2026-05-30T15:30');

        Announcement::query()->create([
            'scope' => AnnouncementScope::Global,
            'category' => AnnouncementCategory::Editorial,
            'type' => AnnouncementType::Info,
            'status' => AnnouncementStatus::Published,
            'title' => 'Should be hidden',
            'body' => 'Past expiry.',
            'expires_at' => $expiresAt,
        ]);

        $this->assertTrue(Announcement::query()->firstOrFail()->isExpired());
        $this->assertSame([], JournalAnnouncementsPayload::forJournal($journal));

        $base = (string) config('journal.base_domain');

        $this->get("http://expiry.{$base}/")
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->has('announcements', 0));
    }
}
