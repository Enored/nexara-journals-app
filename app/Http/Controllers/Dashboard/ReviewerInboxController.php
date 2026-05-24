<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\JournalRole;
use App\Enums\ReviewAssignmentStatus;
use App\Http\Controllers\Concerns\ReturnsDashListPartial;
use App\Http\Controllers\Controller;
use App\Models\ReviewAssignment;
use App\Support\ReviewerInboxIndexFilters;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewerInboxController extends Controller
{
    use ReturnsDashListPartial;

    public function __invoke(Request $request): View
    {
        $user = auth()->user();

        abort_unless(
            $user->journalUserRoles()->where('role', JournalRole::Reviewer)->exists(),
            403
        );

        $reviewerJournals = $user->journalUserRoles()
            ->where('role', JournalRole::Reviewer)
            ->with('journal')
            ->get()
            ->pluck('journal')
            ->unique('id')
            ->sortBy('name')
            ->values();

        $filters = ReviewerInboxIndexFilters::fromRequest($request, $reviewerJournals);
        $assignments = ReviewerInboxIndexFilters::paginate($filters, $user->id);

        $statsBase = ReviewAssignment::query()->where('reviewer_id', $user->id);
        $stats = [
            'invited' => (clone $statsBase)->where('status', ReviewAssignmentStatus::Invited)->count(),
            'active' => (clone $statsBase)->where('status', ReviewAssignmentStatus::Accepted)->count(),
            'completed' => (clone $statsBase)->where('status', ReviewAssignmentStatus::Completed)->count(),
            'overdue' => (clone $statsBase)
                ->whereIn('status', [ReviewAssignmentStatus::Invited, ReviewAssignmentStatus::Accepted])
                ->where('deadline', '<', now()->toDateString())
                ->count(),
            'total' => (clone $statsBase)->count(),
        ];

        $data = [
            'user' => $user,
            'assignments' => $assignments,
            'reviewerJournals' => $reviewerJournals,
            'assignmentStatuses' => ReviewAssignmentStatus::cases(),
            'filters' => $filters,
            'activeFilterPills' => ReviewerInboxIndexFilters::activeFilterPills($filters, $reviewerJournals),
            'hasActiveFilters' => ReviewerInboxIndexFilters::hasActiveFilters($filters),
            'stats' => $stats,
        ];

        return $this->dashListResponse(
            $request,
            'dashboard.reviewer.partials.inbox-list',
            'dashboard.reviewer.inbox',
            $data,
        );
    }
}
