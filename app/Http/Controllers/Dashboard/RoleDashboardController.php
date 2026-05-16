<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\JournalRole;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class RoleDashboardController extends Controller
{
    public function author(): View
    {
        return $this->render(
            activeNav: 'author-dashboard',
            title: 'Author dashboard',
            pageTitle: 'Author dashboard',
            pageDescription: 'Overview and shortcuts for your manuscripts.',
        );
    }

    public function editor(): View
    {
        abort_unless(
            auth()->user()->journalUserRoles()->where('role', JournalRole::Editor)->exists(),
            403
        );

        return $this->render(
            activeNav: 'editor-dashboard',
            title: 'Editor dashboard',
            pageTitle: 'Editor dashboard',
            pageDescription: 'Overview and shortcuts for editorial work.',
        );
    }

    public function reviewer(): View
    {
        abort_unless(
            auth()->user()->journalUserRoles()->where('role', JournalRole::Reviewer)->exists(),
            403
        );

        return $this->render(
            activeNav: 'reviewer-dashboard',
            title: 'Reviewer dashboard',
            pageTitle: 'Reviewer dashboard',
            pageDescription: 'Overview and shortcuts for peer review.',
        );
    }

    public function admin(): View
    {
        return $this->render(
            activeNav: 'admin-dashboard',
            title: 'Admin dashboard',
            pageTitle: 'Platform administration',
            pageDescription: 'Cross-journal metrics and configuration.',
        );
    }

    private function render(string $activeNav, string $title, string $pageTitle, string $pageDescription): View
    {
        return view('dashboard.coming-soon', compact('activeNav', 'title', 'pageTitle', 'pageDescription'));
    }
}
