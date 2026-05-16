<?php

namespace App\Policies;

use App\Models\ReviewAssignment;
use App\Models\User;

class ReviewAssignmentPolicy
{
    public function view(User $user, ReviewAssignment $assignment): bool
    {
        if ($user->isPlatformAdmin()) {
            return true;
        }

        return $assignment->reviewer_id === $user->id;
    }

    public function submit(User $user, ReviewAssignment $assignment): bool
    {
        return $assignment->reviewer_id === $user->id;
    }

    /**
     * Accept or decline an invitation while status is invited.
     */
    public function respond(User $user, ReviewAssignment $assignment): bool
    {
        return $assignment->reviewer_id === $user->id;
    }
}
