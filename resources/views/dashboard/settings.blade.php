@extends('layouts.dashboard', ['activeNav' => 'settings'])

@section('title', 'Account settings')
@section('pageTitle', 'Account settings')
@section('pageDescription', 'Manage your profile and password.')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <section class="dash-card p-6 sm:p-8">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-start">
                <div class="flex flex-col items-center gap-3 sm:items-start">
                    <div class="flex h-24 w-24 items-center justify-center rounded-full bg-teal-100 text-2xl font-semibold text-teal-800">
                        {{ $user->initials() }}
                    </div>
                    <button
                        type="button"
                        disabled
                        class="cursor-not-allowed rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm font-medium text-slate-400"
                        title="Profile photo upload is not available yet"
                    >
                        Change photo
                    </button>
                    <p class="max-w-[12rem] text-center text-xs text-slate-500 sm:text-left">Photo upload coming soon.</p>
                </div>

                <form method="POST" action="{{ platform_route('settings.profile.update') }}" class="min-w-0 flex-1 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-dash.input name="first_name" label="First name" :value="old('first_name', $user->first_name)" required autocomplete="given-name" />
                        <x-dash.input name="last_name" label="Last name" :value="old('last_name', $user->last_name)" required autocomplete="family-name" />
                    </div>

                    <div>
                        <label for="settings-email" class="dash-field-label">Email</label>
                        <input
                            type="email"
                            id="settings-email"
                            value="{{ $user->email }}"
                            disabled
                            class="dash-input mt-1 cursor-not-allowed bg-slate-50 text-slate-500"
                        >
                        <p class="mt-1 text-xs text-slate-500">Email cannot be changed here. Contact support if you need to update it.</p>
                    </div>

                    <x-dash.input name="mobile" label="Mobile number" type="tel" :value="old('mobile', $user->mobile)" autocomplete="tel" />

                    <x-dash.textarea name="bio" label="Bio" rows="4" class="mt-1">{{ old('bio', $user->bio) }}</x-dash.textarea>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-dash.input name="city" label="City" :value="old('city', $user->city)" autocomplete="address-level2" />
                        <x-dash.input name="country" label="Country" :value="old('country', $user->country)" autocomplete="country-name" />
                    </div>

                    <div class="flex justify-end pt-2">
                        <x-dash.button type="submit">Save profile</x-dash.button>
                    </div>
                </form>
            </div>
        </section>

        <section class="dash-card p-6 sm:p-8">
            <h2 class="text-lg font-semibold text-slate-900">Password</h2>
            <p class="mt-1 text-sm text-slate-600">Choose a strong password you do not use elsewhere.</p>

            <form method="POST" action="{{ platform_route('settings.password.update') }}" class="mt-6 max-w-md space-y-4">
                @csrf
                @method('PUT')

                <x-dash.input name="current_password" label="Current password" type="password" required autocomplete="current-password" />
                <x-dash.input name="password" label="New password" type="password" required autocomplete="new-password" />
                <x-dash.input name="password_confirmation" label="Confirm new password" type="password" required autocomplete="new-password" />

                <div class="flex justify-end pt-2">
                    <x-dash.button type="submit" variant="secondary">Update password</x-dash.button>
                </div>
            </form>
        </section>
    </div>
@endsection
