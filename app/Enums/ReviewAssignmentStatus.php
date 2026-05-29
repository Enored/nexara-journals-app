<?php

namespace App\Enums;

enum ReviewAssignmentStatus: string
{
    case Assigned = 'assigned';
    case Completed = 'completed';
    case Expired = 'expired';
}
