<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AuthPagePayload;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Platform/Auth/Register', AuthPagePayload::forRegister());
    }

    public function store(Request $request): HttpResponse|RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            'country' => ['nullable', 'string', 'max:100', Rule::in(config('countries'))],
            'role' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::query()->create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'name' => trim($data['first_name'].' '.$data['last_name']),
            'email' => $data['email'],
            'password' => $data['password'],
            'country' => $data['country'] ?? null,
            'expertise' => isset($data['role']) && $data['role'] !== ''
                ? ['role' => $data['role']]
                : null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return Inertia::location(platform_route('dashboard'));
    }
}
