<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

final class Impersonation
{
    public const SESSION_IMPERSONATOR_ID = 'impersonator_id';

    public static function isActive(): bool
    {
        return session()->has(self::SESSION_IMPERSONATOR_ID);
    }

    public static function impersonatorId(): ?string
    {
        $id = session(self::SESSION_IMPERSONATOR_ID);

        return is_string($id) && $id !== '' ? $id : null;
    }

    public static function impersonator(): ?User
    {
        $id = self::impersonatorId();

        return $id ? User::query()->find($id) : null;
    }

    /**
     * @throws ValidationException
     */
    public static function start(User $admin, User $target): void
    {
        self::assertCanImpersonate($admin, $target);

        session()->put(self::SESSION_IMPERSONATOR_ID, $admin->id);
        Auth::login($target);
        session()->regenerate();
    }

    public static function stop(): User
    {
        $admin = self::impersonator();

        if (! $admin) {
            abort(403, 'No active impersonation session.');
        }

        session()->forget(self::SESSION_IMPERSONATOR_ID);
        Auth::login($admin);
        session()->regenerate();

        return $admin;
    }

    /**
     * @throws ValidationException
     */
    public static function assertCanImpersonate(User $admin, User $target): void
    {
        if (! $admin->isPlatformAdmin()) {
            throw ValidationException::withMessages([
                'user' => 'Only platform administrators can impersonate users.',
            ]);
        }

        if ($admin->id === $target->id) {
            throw ValidationException::withMessages([
                'user' => 'You cannot impersonate yourself.',
            ]);
        }

        if ($target->isPlatformAdmin()) {
            throw ValidationException::withMessages([
                'user' => 'Platform administrator accounts cannot be impersonated.',
            ]);
        }

        if (! $target->is_active) {
            throw ValidationException::withMessages([
                'user' => 'Suspended accounts cannot be impersonated.',
            ]);
        }

        if (self::isActive()) {
            throw ValidationException::withMessages([
                'user' => 'Stop the current impersonation session before starting another.',
            ]);
        }
    }
}
