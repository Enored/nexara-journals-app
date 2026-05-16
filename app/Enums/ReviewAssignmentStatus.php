<?php

namespace App\Enums;

enum ReviewAssignmentStatus: string
{
    case Invited = 'invited';
    case Accepted = 'accepted';
    case Declined = 'declined';
    case Completed = 'completed';
    case Expired = 'expired';
}
