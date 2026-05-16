<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\JournalRole;
use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Support\EditorPipelineIndexFilters;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EditorPipelineController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = auth()->user();

        $journalIds = $user->journalUserRoles()
            ->where('role', JournalRole::Editor)
            ->pluck('journal_id');

        if ($journalIds->isEmpty()) {
            abort(403);
        }

        $editorJournals = $user->journalUserRoles()
            ->where('role', JournalRole::Editor)
            ->with('journal')
            ->get()
            ->pluck('journal');

        $filters = EditorPipelineIndexFilters::fromRequest($request, $editorJournals);

        $statsBase = Submission::query()->whereIn('journal_id', $journalIds);
        $stats = [
            'total' => (clone $statsBase)->count(),
            'pipeline' => (clone $statsBase)->whereIn('status', [
                SubmissionStatus::Submitted,
                SubmissionStatus::UnderReview,
                SubmissionStatus::RevisionRequested,
            ])->count(),
            'under_review' => (clone $statsBase)->where('status', SubmissionStatus::UnderReview)->count(),
            'accepted' => (clone $statsBase)->where('status', SubmissionStatus::Accepted)->count(),
        ];

        $submissions = EditorPipelineIndexFilters::paginate($filters, $journalIds);

        return view('dashboard.editor.pipeline', [
            'user' => $user,
            'submissions' => $submissions,
            'editorJournals' => $editorJournals,
            'statuses' => SubmissionStatus::cases(),
            'filters' => $filters,
            'activeFilterPills' => EditorPipelineIndexFilters::activeFilterPills($filters, $editorJournals),
            'hasActiveFilters' => EditorPipelineIndexFilters::hasActiveFilters($filters),
            'stats' => $stats,
        ]);
    }
}
