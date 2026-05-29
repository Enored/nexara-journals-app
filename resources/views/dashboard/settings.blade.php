@extends('layouts.dashboard', ['activeNav' => 'settings'])

@section('title', 'Account settings')
@section('pageTitle', 'Account settings')
@section('pageDescription', 'Manage your profile and password.')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-0">
                <div class="card-body">
                    <div class="d-flex flex-column flex-sm-row align-items-start gap-4">
                        <div class="text-center text-sm-start">
                            <span class="avatar-xxl rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center fs-2 fw-bold mb-3">
                                {{ $user->initials() }}
                            </span>
                            <div>
                                <button type="button" disabled class="btn btn-sm btn-light disabled opacity-50" title="Profile photo upload is not available yet">
                                    Change photo
                                </button>
                                <p class="text-muted fs-xs mt-2 mb-0">Photo upload coming soon.</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ platform_route('settings.profile.update') }}" class="flex-grow-1">
                            @csrf
                            @method('PUT')

                            <h5 class="mb-3 text-uppercase bg-light-subtle p-2 border border-light border-dashed rounded d-flex align-items-center gap-2">
                                <i data-lucide="circle-user-round" class="fs-lg"></i>
                                Personal info
                            </h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <x-dash.input name="first_name" label="First name" :value="old('first_name', $user->first_name)" required autocomplete="given-name" />
                                </div>
                                <div class="col-md-6">
                                    <x-dash.input name="last_name" label="Last name" :value="old('last_name', $user->last_name)" required autocomplete="family-name" />
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="settings-email" class="form-label">Email address</label>
                                <input type="email" id="settings-email" value="{{ $user->email }}" disabled class="form-control bg-light">
                                <span class="form-text fs-xs text-muted">Email cannot be changed here. Contact support if you need to update it.</span>
                            </div>

                            <x-dash.input name="mobile" label="Mobile number" type="tel" :value="old('mobile', $user->mobile)" autocomplete="tel" />

                            <x-dash.textarea name="bio" label="Bio" rows="4">{{ old('bio', $user->bio) }}</x-dash.textarea>

                            <div class="row">
                                <div class="col-md-6">
                                    <x-dash.input name="city" label="City" :value="old('city', $user->city)" autocomplete="address-level2" />
                                </div>
                                <div class="col-md-6">
                                    <x-dash.input name="country" label="Country" :value="old('country', $user->country)" autocomplete="country-name" />
                                </div>
                            </div>

                            <div class="text-end">
                                <x-dash.button type="submit">Save profile</x-dash.button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3 text-uppercase bg-light-subtle p-2 border border-light border-dashed rounded d-flex align-items-center gap-2">
                        <i data-lucide="lock-keyhole" class="fs-lg"></i>
                        Password
                    </h5>
                    <p class="text-muted">Choose a strong password you do not use elsewhere.</p>

                    <form method="POST" action="{{ platform_route('settings.password.update') }}" class="row g-3">
                        @csrf
                        @method('PUT')

                        <div class="col-md-4">
                            <x-dash.input name="current_password" label="Current password" type="password" required autocomplete="current-password" />
                        </div>
                        <div class="col-md-4">
                            <x-dash.input name="password" label="New password" type="password" required autocomplete="new-password" />
                        </div>
                        <div class="col-md-4">
                            <x-dash.input name="password_confirmation" label="Confirm new password" type="password" required autocomplete="new-password" />
                        </div>
                        <div class="col-12 text-end">
                            <x-dash.button type="submit" variant="secondary">Update password</x-dash.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
