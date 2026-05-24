<?php

namespace App\Support;

use App\Models\AdminAuditLog;
use App\Models\User;
use Illuminate\Http\Request;

final class AdminAuditLogger
{
    public const USER_SUSPENDED = 'user.suspended';

    public const USER_UNSUSPENDED = 'user.unsuspended';

    public const USER_IMPERSONATION_STARTED = 'user.impersonation.started';

    public const USER_IMPERSONATION_STOPPED = 'user.impersonation.stopped';

    public const USER_IMPORTED = 'user.imported';

    /**
     * @param  array<string, mixed>  $metadata
     */
    public static function log(
        string $action,
        ?User $actor,
        ?User $subject = null,
        array $metadata = [],
        ?Request $request = null,
    ): AdminAuditLog {
        $request ??= request();

        return AdminAuditLog::query()->create([
            'actor_id' => $actor?->id,
            'action' => $action,
            'subject_user_id' => $subject?->id,
            'metadata' => $metadata === [] ? null : $metadata,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}
