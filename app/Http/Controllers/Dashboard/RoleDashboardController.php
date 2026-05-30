<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\JournalRole;
use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Submission;
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
        $user = auth()->user();

        abort_unless(
            $user->journalUserRoles()->where('role', JournalRole::Editor)->exists(),
            403
        );

        $journalIds = $user->journalUserRoles()
            ->where('role', JournalRole::Editor)
            ->pluck('journal_id');

        $statsBase = Submission::query()->whereIn('journal_id', $journalIds);

        return view('dashboard.editor.dashboard', [
            'activeNav' => 'editor-dashboard',
            'title' => 'Editor dashboard',
            'pageTitle' => 'Editor dashboard',
            'pageDescription' => 'Overview and shortcuts for editorial work.',
            'stats' => [
                'in_progress' => (clone $statsBase)->whereIn('status', [
                    SubmissionStatus::Screening,
                    SubmissionStatus::Submitted,
                    SubmissionStatus::UnderReview,
                    SubmissionStatus::RevisionRequested,
                ])->count(),
                'accepted' => (clone $statsBase)->where('status', SubmissionStatus::Accepted)->count(),
                'journals' => $journalIds->count(),
            ],
        ]);
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
