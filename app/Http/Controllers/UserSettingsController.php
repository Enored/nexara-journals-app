<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserPasswordRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserSettingsController extends Controller
{
    public function edit(): View
    {
        return view('dashboard.settings', [
            'user' => auth()->user(),
        ]);
    }

    public function updateProfile(UpdateUserProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $user->fill([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'mobile' => $data['mobile'] ?? null,
            'bio' => $data['bio'] ?? null,
            'city' => $data['city'] ?? null,
            'country' => $data['country'] ?? null,
        ]);
        $user->syncDisplayName();
        $user->save();

        return redirect()
            ->route('settings.edit')
            ->with('status', 'Profile updated.');
    }

    public function updatePassword(UpdateUserPasswordRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->password = $request->validated('password');
        $user->save();

        return redirect()
            ->route('settings.edit')
            ->with('status', 'Password updated.');
    }
}
