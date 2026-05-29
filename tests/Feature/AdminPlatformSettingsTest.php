<?php

namespace Tests\Feature;

use App\Http\Middleware\RedirectPlatformRoutesToApex;
use App\Models\PlatformSetting;
use App\Models\User;
use App\Support\AdminAuditLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPlatformSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_platform_admin_can_view_and_update_system_settings(): void
    {
        $admin = User::factory()->platformAdmin()->create();

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($admin)
            ->get(route('admin.settings.edit', absolute: false))
            ->assertOk()
            ->assertSee('System settings')
            ->assertSee('Maintenance mode');

        $this->actingAs($admin)
            ->put(route('admin.settings.branding.update', absolute: false), [
                'platform_name' => 'Nexara Publishing',
            ])
            ->assertRedirect(route('admin.settings.edit', absolute: false))
            ->assertSessionHas('status');

        PlatformSetting::clearCache();
        $this->assertSame('Nexara Publishing', PlatformSetting::current()->platform_name);

        $this->actingAs($admin)
            ->put(route('admin.settings.general.update', absolute: false), [
                'maintenance_mode' => '0',
            ])
            ->assertRedirect(route('admin.settings.edit', absolute: false))
            ->assertSessionHas('status');

        PlatformSetting::clearCache();
        $this->assertFalse(PlatformSetting::current()->maintenance_mode);

        $this->assertDatabaseHas('admin_audit_logs', [
            'actor_id' => $admin->id,
            'action' => AdminAuditLogger::PLATFORM_SETTINGS_UPDATED,
        ]);
    }

    public function test_maintenance_mode_blocks_guests_but_allows_platform_admin(): void
    {
        PlatformSetting::query()->create([
            'id' => PlatformSetting::ROW_ID,
            'platform_name' => 'Test Platform',
            'maintenance_mode' => true,
        ]);
        PlatformSetting::clearCache();

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->get(route('home', absolute: false))
            ->assertStatus(503)
            ->assertSee('under maintenance');

        $admin = User::factory()->platformAdmin()->create();

        $this->actingAs($admin)
            ->get(route('home', absolute: false))
            ->assertOk();

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('home', absolute: false))
            ->assertStatus(503);
    }

    public function test_non_admin_cannot_access_system_settings(): void
    {
        $user = User::factory()->create();

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->actingAs($user)
            ->get(route('admin.settings.edit', absolute: false))
            ->assertForbidden();
    }
}
