<?php

namespace App\Support;

use App\Enums\JournalRole;
use App\Models\User;

class DashboardNavigation
{
    /**
     * @return array{
     *     showRolePicker: bool,
     *     sectionTitle: string,
     *     activeRole: string,
     *     activeRoleMeta: array{key: string, label: string, route: string, icon: string},
     *     roles: list<array{key: string, label: string, route: string, icon: string}>,
     *     items: list<array{key: string, label: string, route: string, icon: string}>
     * }
     */
    public static function forUser(User $user, string $activeNav = ''): array
    {
        $roles = self::rolesFor($user);
        $activeRole = self::resolveActiveRole($activeNav);
        $showRolePicker = $activeNav === 'overview';

        $menus = [
            'overview' => [
                ['key' => 'overview', 'label' => 'Home', 'route' => platform_route('dashboard'), 'icon' => 'layout-dashboard'],
            ],
            'author' => [
                ['key' => 'author-dashboard', 'label' => 'Dashboard', 'route' => platform_route('author.dashboard'), 'icon' => 'layout-dashboard'],
                ['key' => 'author-submissions', 'label' => 'My submissions', 'route' => platform_route('author.submissions'), 'icon' => 'file-text'],
            ],
            'reviewer' => [
                ['key' => 'reviewer-dashboard', 'label' => 'Dashboard', 'route' => platform_route('reviewer.dashboard'), 'icon' => 'layout-dashboard'],
                ['key' => 'reviewer-inbox', 'label' => 'Review inbox', 'route' => platform_route('reviewer.inbox'), 'icon' => 'inbox'],
            ],
            'editor' => [
                ['key' => 'editor-dashboard', 'label' => 'Dashboard', 'route' => platform_route('editor.dashboard'), 'icon' => 'layout-dashboard'],
                ['key' => 'editor-pipeline', 'label' => 'Editorial pipeline', 'route' => platform_route('editor.pipeline'), 'icon' => 'git-branch'],
            ],
            'admin' => [
                ['key' => 'admin-dashboard', 'label' => 'Dashboard', 'route' => platform_route('admin.dashboard'), 'icon' => 'layout-dashboard'],
                ['key' => 'admin-journals', 'label' => 'Journals', 'route' => platform_route('admin.journals.index'), 'icon' => 'book-open'],
                ['key' => 'admin-users', 'label' => 'Users & roles', 'route' => platform_route('admin.users.index'), 'icon' => 'users'],
                ['key' => 'admin-blogs', 'label' => 'Blogs', 'route' => platform_route('admin.blogs.index'), 'icon' => 'notebook-pen'],
                ['key' => 'admin-settings', 'label' => 'System settings', 'route' => platform_route('admin.settings.edit'), 'icon' => 'settings'],
            ],
        ];

        if (! array_key_exists($activeRole, $menus)) {
            $activeRole = 'overview';
        }

        $activeRoleMeta = collect($roles)->firstWhere('key', $activeRole) ?? $roles[0];

        if ($activeNav === 'settings') {
            $activeRoleMeta = [
                'key' => 'account',
                'label' => 'Account',
                'route' => platform_route('settings.edit'),
                'icon' => 'settings',
            ];
            $items = [
                ['key' => 'settings', 'label' => 'Account settings', 'route' => platform_route('settings.edit'), 'icon' => 'settings'],
            ];
            $sectionTitle = 'Account';
        } else {
            $items = $menus[$activeRole];
            $sectionTitle = $activeRoleMeta['label'];
        }

        return [
            'showRolePicker' => $showRolePicker,
            'sectionTitle' => $sectionTitle,
            'activeRole' => $activeRole,
            'activeRoleMeta' => $activeRoleMeta,
            'roles' => $roles,
            'items' => $items,
        ];
    }

    /**
     * @return list<array{key: string, label: string, route: string, icon: string}>
     */
    private static function rolesFor(User $user): array
    {
        $hasEditor = $user->journalUserRoles()->where('role', JournalRole::Editor)->exists();
        $hasReviewer = $user->journalUserRoles()->where('role', JournalRole::Reviewer)->exists();
        $isAdmin = $user->isPlatformAdmin();

        $roles = [
            [
                'key' => 'overview',
                'label' => 'Home',
                'route' => platform_route('dashboard'),
                'icon' => 'home',
            ],
            [
                'key' => 'author',
                'label' => 'Author',
                'route' => platform_route('author.dashboard'),
                'icon' => 'pen-line',
            ],
        ];

        if ($hasReviewer) {
            $roles[] = [
                'key' => 'reviewer',
                'label' => 'Reviewer',
                'route' => platform_route('reviewer.dashboard'),
                'icon' => 'eye',
            ];
        }

        if ($hasEditor) {
            $roles[] = [
                'key' => 'editor',
                'label' => 'Editor',
                'route' => platform_route('editor.dashboard'),
                'icon' => 'git-branch',
            ];
        }

        if ($isAdmin) {
            $roles[] = [
                'key' => 'admin',
                'label' => 'Admin',
                'route' => platform_route('admin.dashboard'),
                'icon' => 'shield',
            ];
        }

        return $roles;
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

        if ($activeNav === 'settings') {
            return 'overview';
        }

        return match ($activeNav) {
            'author' => 'author',
            'reviewer' => 'reviewer',
            'editor' => 'editor',
            default => 'overview',
        };
    }
}
