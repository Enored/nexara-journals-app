<?php

namespace App\Support;

use App\Enums\JournalRole;
use App\Models\Journal;
use App\Models\JournalUserRole;
use App\Models\User;

final class AdminUserRoleSynchronizer
{
    /**
     * Replace all assignable journal roles for a user.
     *
     * @param  array<string, array<string, mixed>>  $rolesInput  journal_id => [role_value => truthy]
     */
    public static function sync(User $user, array $rolesInput, ?string $assignedBy = null): void
    {
        JournalUserRole::query()->where('user_id', $user->id)->delete();

        foreach (Journal::query()->cursor() as $journal) {
            $flags = $rolesInput[$journal->id] ?? [];

            foreach (JournalRole::assignable() as $role) {
                if (! empty($flags[$role->value])) {
                    JournalUserRole::query()->create([
                        'user_id' => $user->id,
                        'journal_id' => $journal->id,
                        'role' => $role,
                        'assigned_by' => $assignedBy,
                    ]);
                }
            }
        }
    }

    /**
     * @param  list<array{journal_id: string, role: JournalRole}>  $assignments
     */
    public static function syncAssignments(User $user, array $assignments, ?string $assignedBy = null): void
    {
        $rolesInput = [];

        foreach ($assignments as $assignment) {
            $journalId = $assignment['journal_id'];
            $rolesInput[$journalId] ??= [];
            $rolesInput[$journalId][$assignment['role']->value] = '1';
        }

        self::sync($user, $rolesInput, $assignedBy);
    }
}
