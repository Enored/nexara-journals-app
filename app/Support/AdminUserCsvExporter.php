<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Collection;

final class AdminUserCsvExporter
{
    /**
     * @param  Collection<int, User>  $users
     */
    public static function stream(Collection $users): void
    {
        $handle = fopen('php://output', 'w');

        if ($handle === false) {
            return;
        }

        // UTF-8 BOM for Excel
        fwrite($handle, "\xEF\xBB\xBF");

        fputcsv($handle, ['Name', 'Email', 'Platform admin', 'Status', 'Journal roles']);

        foreach ($users as $user) {
            $roles = $user->staffJournalRoles
                ->map(fn ($assignment) => $assignment->journal->subdomain.' · '.$assignment->role->label())
                ->join('; ');

            fputcsv($handle, [
                $user->name,
                $user->email,
                $user->is_platform_admin ? 'Yes' : 'No',
                $user->is_active ? 'Active' : 'Suspended',
                $roles,
            ]);
        }

        fclose($handle);
    }
}
