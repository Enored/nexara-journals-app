<?php

namespace Tests\Feature;

use App\Http\Middleware\RedirectPlatformRoutesToApex;
use App\Models\User;
use App\Support\AdminAuditLogger;
use App\Support\AdminUserCsvExporter;
use App\Support\Impersonation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function asPlatformAdmin(User $admin = null): User
    {
        $admin ??= User::factory()->platformAdmin()->create();

        return $admin;
    }

    public function test_platform_admin_can_suspend_and_unsuspend_user(): void
    {
        $admin = $this->asPlatformAdmin();
        $user = User::factory()->create(['is_active' => true]);

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($admin)
            ->post(route('admin.users.suspend', $user))
            ->assertRedirect(route('admin.users.index', absolute: false))
            ->assertSessionHas('status');

        $user->refresh();
        $this->assertFalse($user->is_active);

        $this->actingAs($admin)
            ->post(route('admin.users.unsuspend', $user))
            ->assertRedirect(route('admin.users.index', absolute: false))
            ->assertSessionHas('status');

        $this->assertTrue($user->fresh()->is_active);

        $this->assertDatabaseHas('admin_audit_logs', [
            'actor_id' => $admin->id,
            'subject_user_id' => $user->id,
            'action' => AdminAuditLogger::USER_SUSPENDED,
        ]);
    }

    public function test_suspended_user_cannot_log_in_via_web(): void
    {
        $user = User::factory()->inactive()->create([
            'email' => 'suspended@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->assertFalse($user->fresh()->is_active);

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->from('/login')
            ->post('/login', [
                'email' => 'suspended@example.com',
                'password' => 'password',
            ])
            ->assertSessionHasErrors('email')
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_platform_admin_can_impersonate_user_and_stop(): void
    {
        $admin = $this->asPlatformAdmin();
        $user = User::factory()->create(['is_active' => true]);

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($admin)
            ->post(route('admin.users.impersonate', $user))
            ->assertRedirect(route('dashboard', absolute: false))
            ->assertSessionHas('status');

        $this->assertTrue(Impersonation::isActive());
        $this->assertAuthenticatedAs($user);

        $this->post(route('admin.impersonation.stop'))
            ->assertRedirect(route('admin.users.index', absolute: false))
            ->assertSessionHas('status');

        $this->assertFalse(Impersonation::isActive());
        $this->assertAuthenticatedAs($admin);

        $this->assertDatabaseHas('admin_audit_logs', [
            'actor_id' => $admin->id,
            'subject_user_id' => $user->id,
            'action' => AdminAuditLogger::USER_IMPERSONATION_STARTED,
        ]);
    }

    public function test_cannot_impersonate_platform_admin(): void
    {
        $admin = $this->asPlatformAdmin();
        $otherAdmin = User::factory()->platformAdmin()->create();

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($admin)
            ->post(route('admin.users.impersonate', $otherAdmin))
            ->assertSessionHasErrors('user');

        $this->assertFalse(Impersonation::isActive());
        $this->assertAuthenticatedAs($admin);
    }

    public function test_platform_admin_can_import_users_from_csv(): void
    {
        $admin = $this->asPlatformAdmin();

        $csv = "Name,Email,Platform admin,Status,Journal roles\n";
        $csv .= "Imported Person,imported@example.com,No,Active,\n";

        $file = UploadedFile::fake()->createWithContent('users.csv', $csv);

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($admin)
            ->post(route('admin.users.import'), ['file' => $file])
            ->assertRedirect(route('admin.users.index', absolute: false))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'email' => 'imported@example.com',
            'name' => 'Imported Person',
            'is_active' => true,
            'is_platform_admin' => false,
        ]);

        $this->assertDatabaseHas('admin_audit_logs', [
            'actor_id' => $admin->id,
            'action' => AdminAuditLogger::USER_IMPORTED,
        ]);
    }

    public function test_user_export_includes_status_column(): void
    {
        $user = User::factory()->inactive()->create([
            'name' => 'Suspended User',
            'email' => 'inactive@example.com',
        ]);

        ob_start();
        AdminUserCsvExporter::stream(collect([$user]));
        $output = ob_get_clean();

        $this->assertStringContainsString('Status', $output);
        $this->assertStringContainsString('Suspended', $output);
        $this->assertStringContainsString('inactive@example.com', $output);
    }

    public function test_last_platform_admin_cannot_be_suspended(): void
    {
        $admin = User::factory()->platformAdmin()->create();

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($admin)
            ->post(route('admin.users.suspend', $admin))
            ->assertSessionHasErrors('user');
    }
}
