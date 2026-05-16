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
        if ($assignment->status !== ReviewAssignmentStatus::Invited) {
            return view('reviews.invitation-closed', ['assignment' => $assignment]);
        }

        $assignment->update([
            'status' => ReviewAssignmentStatus::Accepted,
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
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:5000'],
        ]);

        if ($assignment->status !== ReviewAssignmentStatus::Invited) {
            return redirect()->back()->withErrors(['invitation' => 'This invitation is no longer active.']);
        }

        $assignment->update([
            'status' => ReviewAssignmentStatus::Declined,
            'responded_at' => now(),
            'decline_reason' => $data['reason'] ?? null,
        ]);

        WorkflowNotification::query()->create([
            'user_id' => $assignment->editor_id,
            'type' => 'reviewer_declined',
            'data' => [
                'submission_id' => $assignment->submission_id,
                'assignment_id' => $assignment->id,
            ],
        ]);

        return redirect()->route('home')->with('status', 'You have declined the review invitation.');
    }
}
