<?php

namespace App\Support;

use App\Enums\JournalRole;
use App\Models\User;

class DashboardNavigation
{
    /**
     * @return array{activeRole: string, roles: list<array{key: string, label: string, route: string}>, items: list<array{key: string, label: string, route: string, icon: string}>}
     */
    public static function forUser(User $user, string $activeNav = ''): array
    {
        $hasEditor = $user->journalUserRoles()->where('role', JournalRole::Editor)->exists();
        $hasReviewer = $user->journalUserRoles()->where('role', JournalRole::Reviewer)->exists();
        $isAdmin = $user->isPlatformAdmin();

        $roles = [
            ['key' => 'overview', 'label' => 'Overview', 'route' => platform_route('dashboard')],
            ['key' => 'author', 'label' => 'Author', 'route' => platform_route('author.dashboard')],
        ];

        if ($hasReviewer) {
            $roles[] = ['key' => 'reviewer', 'label' => 'Reviewer', 'route' => platform_route('reviewer.dashboard')];
        }

        if ($hasEditor) {
            $roles[] = ['key' => 'editor', 'label' => 'Editor', 'route' => platform_route('editor.dashboard')];
        }

        if ($isAdmin) {
            $roles[] = ['key' => 'admin', 'label' => 'Admin', 'route' => platform_route('admin.dashboard')];
        }

        $activeRole = self::resolveActiveRole($activeNav);

        $menus = [
            'overview' => [
                ['key' => 'overview', 'label' => 'Home', 'route' => platform_route('dashboard'), 'icon' => 'overview'],
            ],
            'author' => [
                ['key' => 'author-dashboard', 'label' => 'Dashboard', 'route' => platform_route('author.dashboard'), 'icon' => 'overview'],
                ['key' => 'author-submissions', 'label' => 'My submissions', 'route' => platform_route('author.submissions'), 'icon' => 'author'],
            ],
            'reviewer' => [
                ['key' => 'reviewer-dashboard', 'label' => 'Dashboard', 'route' => platform_route('reviewer.dashboard'), 'icon' => 'overview'],
                ['key' => 'reviewer-inbox', 'label' => 'Review inbox', 'route' => platform_route('reviewer.inbox'), 'icon' => 'reviewer'],
            ],
            'editor' => [
                ['key' => 'editor-dashboard', 'label' => 'Dashboard', 'route' => platform_route('editor.dashboard'), 'icon' => 'overview'],
                ['key' => 'editor-pipeline', 'label' => 'Editorial pipeline', 'route' => platform_route('editor.pipeline'), 'icon' => 'editor'],
            ],
            'admin' => [
                ['key' => 'admin-dashboard', 'label' => 'Dashboard', 'route' => platform_route('admin.dashboard'), 'icon' => 'admin'],
                ['key' => 'admin-journals', 'label' => 'Journals', 'route' => platform_route('admin.journals.index'), 'icon' => 'journals'],
                ['key' => 'admin-users', 'label' => 'Users & roles', 'route' => platform_route('admin.users.index'), 'icon' => 'users'],
            ],
        ];

        if (! array_key_exists($activeRole, $menus)) {
            $activeRole = 'overview';
        }

        return [
            'activeRole' => $activeRole,
            'roles' => $roles,
            'items' => $menus[$activeRole],
        ];
    }

    private static function resolveActiveRole(string $activeNav): string
    {
        if ($activeNav === '' || $activeNav === 'overview') {
            return 'overview';
        }

        if (str_starts_with($activeNav, 'admin')) {
            return 'admin';
        }

        if (str_starts_with($activeNav, 'author')) {
            return 'author';
        }

        if (str_starts_with($activeNav, 'editor')) {
            return 'editor';
        }

        if (str_starts_with($activeNav, 'reviewer')) {
            return 'reviewer';
        }

        return match ($activeNav) {
            'author' => 'author',
            'reviewer' => 'reviewer',
            'editor' => 'editor',
            default => 'overview',
        };
    }
}
