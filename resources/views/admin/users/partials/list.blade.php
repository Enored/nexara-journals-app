@php
    use App\Support\AdminUserAccountService;
    use App\Support\AdminUserIndexFilters;

    $listQuery = AdminUserIndexFilters::queryParams($filters);
    $exportUrl = platform_route('admin.users.export', AdminUserIndexFilters::queryParams($filters));
    $currentAdmin = auth()->user();
@endphp

<x-dash.list-card
    :filter-action="platform_route('admin.users.index')"
    :paginator="$users"
    item-label="users"
>
    <x-slot:filterEnd>
        <x-dash.button variant="secondary" data-bs-toggle="modal" data-bs-target="#user-import-modal">
            <i data-lucide="upload" class="fs-sm me-1"></i>
            Import CSV
        </x-dash.button>
        <x-dash.button variant="secondary" :href="$exportUrl">
            <i data-lucide="download" class="fs-sm me-1"></i>
            Export CSV
        </x-dash.button>
    </x-slot:filterEnd>
    <x-slot:filterStart>
        <x-dash.app-search
            type="search"
            name="q"
            id="users-filter-q"
            :value="$filters['q']"
            placeholder="Search name or email…"
        />
        <x-dash.app-search type="select" name="journal" id="users-filter-journal" icon="book-open">
            <option value="">All journals</option>
            @foreach ($journals as $journal)
                <option value="{{ $journal->subdomain }}" @selected($filters['journal'] === $journal->subdomain)>{{ $journal->name }}</option>
            @endforeach
        </x-dash.app-search>
        <x-dash.app-search type="select" name="role" id="users-filter-role" icon="circle-small">
            <option value="">All roles</option>
            @foreach ($roles as $role)
                <option value="{{ $role->value }}" @selected($filters['role'] === $role)>{{ $role->label() }}</option>
            @endforeach
        </x-dash.app-search>
        <x-dash.app-search type="select" name="status" id="users-filter-status" icon="user-check">
            <option value="">All statuses</option>
            <option value="active" @selected($filters['status'] === AdminUserIndexFilters::STATUS_ACTIVE)>Active</option>
            <option value="suspended" @selected($filters['status'] === AdminUserIndexFilters::STATUS_SUSPENDED)>Suspended</option>
        </x-dash.app-search>
        @if ($hasActiveFilters)
            <x-dash.button variant="secondary" :href="platform_route('admin.users.index')" data-dash-partial-link>Reset</x-dash.button>
        @endif
    </x-slot:filterStart>
    @if ($hasActiveFilters)
        <x-slot:pills>
            <x-dash.filter-pills
                :pills="$activeFilterPills"
                :reset-url="platform_route('admin.users.index')"
            />
        </x-slot:pills>
    @endif
    <x-slot:header>
        <tr class="text-uppercase fs-xxs">
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Journal roles</th>
            <th class="text-end">Actions</th>
        </tr>
    </x-slot:header>
    <x-slot:body>
        @forelse ($users as $user)
            @php
                $canManageAccount = $currentAdmin->id !== $user->id
                    && ! ($user->isPlatformAdmin() && AdminUserAccountService::isLastPlatformAdmin($user));
                $canImpersonate = $currentAdmin->id !== $user->id
                    && ! $user->isPlatformAdmin()
                    && $user->is_active;
            @endphp
            <tr @class(['opacity-75' => ! $user->is_active])>
                <td class="fw-medium">
                    {{ $user->name }}
                    @if ($user->is_platform_admin)
                        <span class="badge badge-soft-primary ms-1">Platform admin</span>
                    @endif
                </td>
                <td class="text-muted">{{ $user->email }}</td>
                <td>
                    @if ($user->is_active)
                        <span class="badge badge-soft-success">Active</span>
                    @else
                        <span class="badge badge-soft-danger">Suspended</span>
                    @endif
                </td>
                <td>
                    <div class="d-flex flex-wrap gap-1">
                        @forelse ($user->staffJournalRoles as $jur)
                            <span class="badge badge-soft-secondary" title="{{ $jur->journal->name }}">
                                {{ $jur->journal->subdomain }} · {{ $jur->role->label() }}
                            </span>
                        @empty
                            <span class="text-muted" title="No staff roles; user can still submit manuscripts as an author">—</span>
                        @endforelse
                    </div>
                </td>
                <td class="text-end">
                    <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                        <button
                            type="button"
                            class="btn btn-link btn-sm link-primary fw-medium p-0"
                            data-user-roles-open
                            data-url="{{ platform_route('admin.users.edit-roles', $user) }}?modal=1&amp;{{ http_build_query($listQuery) }}"
                            data-user-name="{{ $user->name }}"
                            data-user-email="{{ $user->email }}"
                        >
                            Edit roles
                        </button>
                        @if ($canManageAccount)
                            @if ($user->is_active)
                                <form
                                    id="user-suspend-form-{{ $user->id }}"
                                    method="POST"
                                    action="{{ platform_route('admin.users.suspend', $user) }}"
                                    class="d-none"
                                >
                                    @csrf
                                    @foreach ($listQuery as $key => $value)
                                        <input type="hidden" name="return[{{ $key }}]" value="{{ $value }}">
                                    @endforeach
                                </form>
                                <button
                                    type="button"
                                    class="btn btn-link btn-sm link-danger fw-medium p-0"
                                    data-admin-confirm-open
                                    data-confirm-form="user-suspend-form-{{ $user->id }}"
                                    data-confirm-title="Suspend user?"
                                    data-confirm-message="Suspend {{ $user->name }}? They will be signed out and unable to log in until reactivated."
                                    data-confirm-label="Suspend user"
                                    data-confirm-variant="danger"
                                >
                                    Suspend
                                </button>
                            @else
                                <form
                                    id="user-unsuspend-form-{{ $user->id }}"
                                    method="POST"
                                    action="{{ platform_route('admin.users.unsuspend', $user) }}"
                                    class="d-none"
                                >
                                    @csrf
                                    @foreach ($listQuery as $key => $value)
                                        <input type="hidden" name="return[{{ $key }}]" value="{{ $value }}">
                                    @endforeach
                                </form>
                                <button
                                    type="button"
                                    class="btn btn-link btn-sm link-success fw-medium p-0"
                                    data-admin-confirm-open
                                    data-confirm-form="user-unsuspend-form-{{ $user->id }}"
                                    data-confirm-title="Reactivate user?"
                                    data-confirm-message="Reactivate {{ $user->name }}? They will be able to sign in again."
                                    data-confirm-label="Reactivate user"
                                    data-confirm-variant="success"
                                >
                                    Unsuspend
                                </button>
                            @endif
                        @endif
                        @if ($canImpersonate)
                            <form
                                id="user-impersonate-form-{{ $user->id }}"
                                method="POST"
                                action="{{ platform_route('admin.users.impersonate', $user) }}"
                                class="d-none"
                            >
                                @csrf
                            </form>
                            <button
                                type="button"
                                class="btn btn-link btn-sm link-secondary fw-medium p-0"
                                data-admin-confirm-open
                                data-confirm-form="user-impersonate-form-{{ $user->id }}"
                                data-confirm-title="Impersonate user?"
                                data-confirm-message="View the platform as {{ $user->name }}? Your admin session will be preserved so you can stop impersonating at any time."
                                data-confirm-label="Start impersonating"
                                data-confirm-variant="warning"
                            >
                                Impersonate
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="p-0">
                    <x-dash.empty
                        title="No users found"
                        :description="$hasActiveFilters ? 'Try adjusting your search or filters.' : 'Users appear here once they register.'"
                    />
                </td>
            </tr>
        @endforelse
    </x-slot:body>
</x-dash.list-card>
