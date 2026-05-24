<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AdminUserAccountService
{
    /**
     * @throws ValidationException
     */
    public static function suspend(User $actor, User $target): void
    {
        self::assertCanManageAccount($actor, $target);

        if (! $target->is_active) {
            throw ValidationException::withMessages([
                'user' => 'This account is already suspended.',
            ]);
        }

        DB::transaction(function () use ($actor, $target) {
            $target->update(['is_active' => false]);
            $target->tokens()->delete();

            AdminAuditLogger::log(
                AdminAuditLogger::USER_SUSPENDED,
                $actor,
                $target,
            );
        });
    }

    /**
     * @throws ValidationException
     */
    public static function unsuspend(User $actor, User $target): void
    {
        self::assertCanManageAccount($actor, $target);

        if ($target->is_active) {
            throw ValidationException::withMessages([
                'user' => 'This account is already active.',
            ]);
        }

        DB::transaction(function () use ($actor, $target) {
            $target->update(['is_active' => true]);

            AdminAuditLogger::log(
                AdminAuditLogger::USER_UNSUSPENDED,
                $actor,
                $target,
            );
        });
    }

    /**
     * @throws ValidationException
     */
    public static function assertCanManageAccount(User $actor, User $target): void
    {
        if (! $actor->isPlatformAdmin()) {
            throw ValidationException::withMessages([
                'user' => 'Only platform administrators can manage user accounts.',
            ]);
        }

        if ($actor->id === $target->id) {
            throw ValidationException::withMessages([
                'user' => 'You cannot suspend or unsuspend your own account.',
            ]);
        }

        if ($target->isPlatformAdmin() && self::isLastPlatformAdmin($target)) {
            throw ValidationException::withMessages([
                'user' => 'The last platform administrator cannot be suspended.',
            ]);
        }
    }

    public static function isLastPlatformAdmin(User $user): bool
    {
        if (! $user->isPlatformAdmin()) {
            return false;
        }

        return User::query()
            ->where('is_platform_admin', true)
            ->where('is_active', true)
            ->count() <= 1;
    }
}
