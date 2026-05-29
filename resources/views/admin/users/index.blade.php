@extends('layouts.dashboard', ['activeNav' => 'admin-users'])

@section('title', 'Users')
@section('pageTitle', 'Users')
@section('pageDescription', 'Search users, create accounts, export CSV, and assign staff roles.')

@section('content')
    <x-dash.list-partial-zone>
        @include('admin.users.partials.list')
    </x-dash.list-partial-zone>
@endsection

@push('modals')
    <x-admin.ajax-modal
        id="user-roles-modal"
        title="Edit staff roles"
        size="modal-lg"
        submit-form="user-roles-form"
        submit-label="Save roles"
    />

    <div
        class="modal fade"
        id="user-create-modal"
        tabindex="-1"
        aria-labelledby="user-create-modal-title"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form
                    method="POST"
                    action="{{ platform_route('admin.users.store') }}"
                    id="user-create-form"
                >
                    @csrf
                    @foreach (\App\Support\AdminUserIndexFilters::queryParamsFromRequest(request()) as $key => $value)
                        <input type="hidden" name="return[{{ $key }}]" value="{{ $value }}">
                    @endforeach
                    <div class="modal-header">
                        <h4 class="modal-title" id="user-create-modal-title">Create user</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3">
                            A secure random password will be generated automatically. You will see it once after the account is created.
                        </p>
                        <x-dash.input
                            label="First name"
                            name="first_name"
                            :value="old('first_name')"
                            required
                        />
                        <x-dash.input
                            label="Last name"
                            name="last_name"
                            :value="old('last_name')"
                            required
                        />
                        <x-dash.input
                            label="Email"
                            name="email"
                            type="email"
                            :value="old('email')"
                            required
                        />
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="hidden" name="is_active" value="0">
                                <input
                                    type="checkbox"
                                    name="is_active"
                                    value="1"
                                    id="user-create-active"
                                    class="form-check-input"
                                    @checked(old('is_active', true))
                                >
                                <label class="form-check-label" for="user-create-active">Active (can sign in)</label>
                            </div>
                        </div>
                        <div class="mb-0">
                            <div class="form-check">
                                <input type="hidden" name="is_platform_admin" value="0">
                                <input
                                    type="checkbox"
                                    name="is_platform_admin"
                                    value="1"
                                    id="user-create-platform-admin"
                                    class="form-check-input"
                                    @checked(old('is_platform_admin'))
                                >
                                <label class="form-check-label" for="user-create-platform-admin">Platform administrator</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create user</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if (session('created_user_password'))
        <div
            class="modal fade"
            id="user-created-modal"
            tabindex="-1"
            aria-labelledby="user-created-modal-title"
            aria-hidden="true"
            data-user-created-modal
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h4 class="modal-title" id="user-created-modal-title">User created</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-2">
                        <p class="mb-3">
                            <strong>{{ session('created_user_name') }}</strong> can now sign in.
                            Share these credentials securely — this password is shown only once.
                        </p>
                        <dl class="row mb-0 small">
                            <dt class="col-sm-3 text-muted">Email</dt>
                            <dd class="col-sm-9 mb-2 font-monospace">{{ session('created_user_email') }}</dd>
                            <dt class="col-sm-3 text-muted">Password</dt>
                            <dd class="col-sm-9 mb-0">
                                <code id="created-user-password" class="user-select-all">{{ session('created_user_password') }}</code>
                            </dd>
                        </dl>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button
                            type="button"
                            class="btn btn-light"
                            data-copy-created-password
                            data-password="{{ session('created_user_password') }}"
                        >
                            Copy password
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Done</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const createdModal = document.getElementById('user-created-modal');
            if (createdModal && window.bootstrap?.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(createdModal).show();
            }

            const createModal = document.getElementById('user-create-modal');
            @if ($errors->hasAny(['first_name', 'last_name', 'email', 'is_platform_admin', 'is_active']))
            if (createModal && window.bootstrap?.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(createModal).show();
            }
            @endif

            document.querySelector('[data-copy-created-password]')?.addEventListener('click', async (event) => {
                const password = event.currentTarget.getAttribute('data-password');
                if (!password) {
                    return;
                }

                try {
                    await navigator.clipboard.writeText(password);
                    if (typeof window.showDashToast === 'function') {
                        window.showDashToast('Password copied to clipboard.', 'success');
                    }
                } catch {
                    if (typeof window.showDashToast === 'function') {
                        window.showDashToast('Could not copy password. Select and copy it manually.', 'error');
                    }
                }
            });
        });
    </script>
@endpush
