<?php

namespace App\Http\Controllers;

use App\Enums\ReviewAssignmentStatus;
use App\Models\ReviewAssignment;
use App\Models\WorkflowNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewInvitationController extends Controller
{
    public function accept(ReviewAssignment $assignment): View
    {
        if ($assignment->status !== ReviewAssignmentStatus::Assigned) {
            return view('reviews.invitation-closed', ['assignment' => $assignment]);
        }

        $assignment->update([
            'status' => ReviewAssignmentStatus::Assigned,
            'responded_at' => now(),
        ]);

        WorkflowNotification::query()->create([
            'user_id' => $assignment->editor_id,
            'type' => 'reviewer_accepted',
            'data' => [
                'submission_id' => $assignment->submission_id,
                'assignment_id' => $assignment->id,
            ],
        ]);

        return view('reviews.invitation-accepted', [
            'assignment' => $assignment->load('submission.journal'),
        ]);
    }

    public function declineForm(ReviewAssignment $assignment): View
    {
        return view('reviews.invitation-decline', [
            'assignment' => $assignment->load('submission.journal'),
        ]);
    }

    public function decline(Request $request, ReviewAssignment $assignment): RedirectResponse
    {
        return redirect()->route('home')->with('status', 'Review assignments cannot be declined.');
    }
}
