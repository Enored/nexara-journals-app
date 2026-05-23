<?php

namespace Tests\Feature;

use App\Enums\JournalRole;
use App\Http\Middleware\RedirectPlatformRoutesToApex;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_platform_admin_cannot_assign_author_role_via_web(): void
    {
        $admin = User::factory()->create(['is_platform_admin' => true]);
        $user = User::factory()->create();
        $journal = Journal::query()->create([
            'name' => 'Test Journal',
            'subdomain' => 'test-journal',
            'is_active' => true,
        ]);

        $response = $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($admin)
            ->put(route('admin.users.update-roles', $user), [
                'roles' => [
                    $journal->id => [
                        JournalRole::Author->value => '1',
                        JournalRole::Reviewer->value => '1',
                    ],
                ],
            ]);

        $response->assertRedirect(route('admin.users.index', absolute: false));
        $response->assertSessionHas('status');

        $this->assertDatabaseMissing('journal_user_roles', [
            'user_id' => $user->id,
            'journal_id' => $journal->id,
            'role' => JournalRole::Author->value,
        ]);

        $this->assertDatabaseHas('journal_user_roles', [
            'user_id' => $user->id,
            'journal_id' => $journal->id,
            'role' => JournalRole::Reviewer->value,
        ]);
    }

    public function test_api_rejects_author_in_role_assignments(): void
    {
        $admin = User::factory()->create(['is_platform_admin' => true]);
        $user = User::factory()->create();
        $journal = Journal::query()->create([
            'name' => 'Test Journal',
            'subdomain' => 'test-journal',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->putJson("/api/admin/users/{$user->id}/roles", [
                'assignments' => [
                    [
                        'journal_id' => $journal->id,
                        'roles' => [JournalRole::Author->value],
                    ],
                ],
            ])
            ->assertUnprocessable();
    }
}
