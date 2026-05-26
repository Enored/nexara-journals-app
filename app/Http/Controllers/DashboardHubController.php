<?php

namespace App\Http\Controllers;

use App\Enums\JournalRole;
use Illuminate\View\View;

class DashboardHubController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $sections = [];
        if ($user->isPlatformAdmin()) {
            $sections[] = ['label' => 'Platform admin', 'description' => 'Journals, users, and platform metrics.', 'route' => platform_route('admin.dashboard'), 'tone' => 'slate'];
        }

        $editorJournals = $user->journalUserRoles()->where('role', JournalRole::Editor)->count();
        if ($editorJournals > 0) {
            $sections[] = ['label' => 'Editor', 'description' => 'Submissions and peer review for your journals.', 'route' => platform_route('editor.submissions'), 'tone' => 'teal'];
        }

        $reviewerRoles = $user->journalUserRoles()->where('role', JournalRole::Reviewer)->count();
        if ($reviewerRoles > 0) {
            $sections[] = ['label' => 'Reviewer', 'description' => 'Pending and completed reviews across journals.', 'route' => platform_route('reviewer.inbox'), 'tone' => 'indigo'];
        }

        $sections[] = ['label' => 'Author', 'description' => 'Your manuscripts and statuses.', 'route' => platform_route('author.submissions'), 'tone' => 'amber'];

        return view('dashboard.index', [
            'user' => $user,
            'sections' => $sections,
        ]);
    }
}
