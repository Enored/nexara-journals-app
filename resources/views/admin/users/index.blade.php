@extends('layouts.dashboard', ['activeNav' => 'admin-users'])

@php
    use App\Support\AdminUserIndexFilters;
    $listQuery = AdminUserIndexFilters::queryParams($filters);
@endphp

@section('title', 'Users')
@section('pageTitle', 'Users')
@section('pageDescription', 'Search users and assign reviewer, editor, or journal admin roles. Every user can submit as an author without assignment.')

@section('content')
    <form method="GET" action="{{ platform_route('admin.users.index') }}" class="dash-card w-full p-4">
        <div class="flex w-full flex-col gap-4 lg:flex-row lg:items-end">
            <div class="min-w-0 w-full flex-[2]">
                <label for="users-filter-q" class="dash-field-label">Search</label>
                <input
                    type="search"
                    id="users-filter-q"
                    name="q"
                    value="{{ $filters['q'] }}"
                    placeholder="Name or email…"
                    class="dash-input"
                />
            </div>
            <div class="min-w-0 w-full flex-1">
                <label for="users-filter-journal" class="dash-field-label">Journal</label>
                <select id="users-filter-journal" name="journal" class="dash-select">
                    <option value="">All journals</option>
                    @foreach ($journals as $journal)
                        <option value="{{ $journal->subdomain }}" @selected($filters['journal'] === $journal->subdomain)>{{ $journal->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-0 w-full flex-1 lg:max-w-xs">
                <label for="users-filter-role" class="dash-field-label">Role</label>
                <select id="users-filter-role" name="role" class="dash-select">
                    <option value="">All roles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->value }}" @selected($filters['role'] === $role)>{{ $role->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex w-full shrink-0 flex-wrap gap-2 lg:w-auto">
                <x-dash.button type="submit" class="w-full sm:w-auto">Apply</x-dash.button>
                @if ($hasActiveFilters)
                    <x-dash.button variant="secondary" :href="platform_route('admin.users.index')" class="w-full sm:w-auto">Reset</x-dash.button>
                @endif
            </div>
        </div>
    </form>

    <x-dash.filter-pills
        class="mt-4"
        :pills="$activeFilterPills"
        :reset-url="$hasActiveFilters ? platform_route('admin.users.index') : null"
    />

    <x-dash.table class="mt-6">
        <x-slot:header>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Journal roles</th>
                <th class="text-right">Actions</th>
            </tr>
        </x-slot:header>
        <x-slot:body>
            @forelse ($users as $user)
                <tr>
                    <td class="font-medium text-slate-900">
                        {{ $user->name }}
                        @if ($user->is_platform_admin)
                            <x-dash.badge class="ml-1 bg-teal-50 text-teal-800">Platform admin</x-dash.badge>
                        @endif
                    </td>
                    <td class="text-slate-600">{{ $user->email }}</td>
                    <td>
                        <div class="flex flex-wrap gap-1">
                            @forelse ($user->staffJournalRoles as $jur)
                                <x-dash.badge title="{{ $jur->journal->name }}">
                                    {{ $jur->journal->subdomain }} · {{ $jur->role->label() }}
                                </x-dash.badge>
                            @empty
                                <span class="text-slate-400" title="No staff roles; user can still submit manuscripts as an author">—</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="text-right">
                        <button
                            type="button"
                            class="dash-link-btn"
                            data-user-roles-open
                            data-url="{{ platform_route('admin.users.edit-roles', $user) }}?modal=1&amp;{{ http_build_query($listQuery) }}"
                            data-user-name="{{ $user->name }}"
                            data-user-email="{{ $user->email }}"
                        >
                            Edit roles
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="!p-0">
                        <x-dash.empty
                            title="No users found"
                            :description="$hasActiveFilters ? 'Try adjusting your search or filters.' : 'Users appear here once they register.'"
                        />
                    </td>
                </tr>
            @endforelse
        </x-slot:body>
    </x-dash.table>

    <x-dash.pagination :paginator="$users" item-label="users" />

    <div
        id="user-roles-modal"
        class="dash-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="user-roles-modal-title"
        aria-hidden="true"
    >
        <button type="button" class="dash-modal-backdrop" data-user-roles-close aria-label="Close dialog"></button>
        <div class="dash-modal-panel sm:max-w-xl">
            <div class="dash-modal-header">
                <div class="min-w-0">
                    <h2 id="user-roles-modal-title" class="text-base font-semibold text-slate-900">Edit staff roles</h2>
                    <p id="user-roles-modal-subtitle" class="mt-0.5 truncate text-sm text-slate-500"></p>
                </div>
                <button
                    type="button"
                    class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                    data-user-roles-close
                    aria-label="Close"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="user-roles-modal-body" class="dash-modal-body">
                <p class="text-sm text-slate-500">Loading…</p>
            </div>
            <div class="dash-modal-footer">
                <x-dash.button variant="secondary" type="button" data-user-roles-close>Cancel</x-dash.button>
                <x-dash.button type="submit" form="user-roles-form">Save roles</x-dash.button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const modal = document.getElementById('user-roles-modal');
    const body = document.getElementById('user-roles-modal-body');
    const subtitle = document.getElementById('user-roles-modal-subtitle');
    if (!modal || !body || !subtitle) {
        return;
    }

    let lastTrigger = null;

    const open = () => {
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    };

    const close = () => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
        body.innerHTML = '<p class="text-sm text-slate-500">Loading…</p>';
        if (lastTrigger) {
            lastTrigger.focus();
            lastTrigger = null;
        }
    };

    const load = async (url, name, email) => {
        subtitle.textContent = name + ' · ' + email;
        body.innerHTML = '<p class="text-sm text-slate-500">Loading…</p>';
        open();

        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'text/html' },
                credentials: 'same-origin',
            });
            if (!response.ok) {
                throw new Error('Failed to load');
            }
            body.innerHTML = await response.text();
        } catch {
            body.innerHTML = '<p class="text-sm text-red-600">Could not load roles. Please try again.</p>';
        }
    };

    document.querySelectorAll('[data-user-roles-open]').forEach((btn) => {
        btn.addEventListener('click', () => {
            lastTrigger = btn;
            load(btn.dataset.url, btn.dataset.userName, btn.dataset.userEmail);
        });
    });

    modal.querySelectorAll('[data-user-roles-close]').forEach((el) => {
        el.addEventListener('click', close);
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('is-open')) {
            close();
        }
    });
})();
</script>
@endpush
