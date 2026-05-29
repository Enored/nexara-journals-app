<?php

namespace Tests\Unit;

use App\Models\User;
use App\Support\DashboardNavigation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_hub_page_shows_role_picker(): void
    {
        $user = User::factory()->create();

        $nav = DashboardNavigation::forUser($user, 'overview');

        $this->assertTrue($nav['showRolePicker']);
        $this->assertSame('Home', collect($nav['roles'])->firstWhere('key', 'overview')['label']);
    }

    public function test_author_page_hides_role_picker_and_shows_workspace_back(): void
    {
        $user = User::factory()->create();

        $nav = DashboardNavigation::forUser($user, 'author-submissions');

        $this->assertFalse($nav['showRolePicker']);
        $this->assertSame('author', $nav['activeRole']);
        $this->assertSame('Author', $nav['sectionTitle']);
    }

    public function test_settings_uses_account_section(): void
    {
        $user = User::factory()->create();

        $nav = DashboardNavigation::forUser($user, 'settings');

        $this->assertFalse($nav['showRolePicker']);
        $this->assertSame('Account', $nav['sectionTitle']);
        $this->assertSame('settings', $nav['items'][0]['key']);
    }
}
