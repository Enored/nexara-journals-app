<?php

namespace App\Policies;

use App\Enums\JournalRole;
use App\Models\Journal;
use App\Models\User;

class JournalPolicy
{
    public function manageEditions(User $user, Journal $journal): bool
    {
        if ($user->isPlatformAdmin()) {
            return true;
        }

        return $user->hasJournalRole($journal, JournalRole::Editor)
            || $user->hasJournalRole($journal, JournalRole::Admin);
    }
}
