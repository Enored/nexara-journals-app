<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class AdminUserCreator
{
    /**
     * @param  array{
     *     first_name: string,
     *     last_name: string,
     *     email: string,
     *     is_platform_admin?: bool,
     *     is_active?: bool
     * }  $data
     * @return array{user: User, plainPassword: string}
     *
     * @throws ValidationException
     */
    public static function create(array $data, User $actor): array
    {
        if (! $actor->isPlatformAdmin()) {
            throw ValidationException::withMessages([
                'user' => 'Only platform administrators can create user accounts.',
            ]);
        }

        $email = mb_strtolower(trim($data['email']));

        if (User::query()->where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'A user with this email already exists.',
            ]);
        }

        $firstName = trim($data['first_name']);
        $lastName = trim($data['last_name']);
        $plainPassword = Str::password(16);
        $isActive = (bool) ($data['is_active'] ?? true);
        $isPlatformAdmin = (bool) ($data['is_platform_admin'] ?? false);

        $user = DB::transaction(function () use ($firstName, $lastName, $email, $plainPassword, $isActive, $isPlatformAdmin, $actor) {
            $user = User::query()->create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'name' => trim($firstName.' '.$lastName),
                'email' => $email,
                'password' => Hash::make($plainPassword),
                'email_verified_at' => now(),
                'is_platform_admin' => $isPlatformAdmin,
                'is_active' => $isActive,
            ]);

            AdminAuditLogger::log(
                AdminAuditLogger::USER_CREATED,
                $actor,
                $user,
                ['email' => $email],
            );

            return $user;
        });

        return [
            'user' => $user,
            'plainPassword' => $plainPassword,
        ];
    }
}
