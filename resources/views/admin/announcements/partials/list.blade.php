@php
    use App\Enums\AnnouncementCategory;
    use App\Enums\AnnouncementScope;
    use App\Enums\AnnouncementStatus;
@endphp

<x-dash.list-card
    :filter-action="platform_route('admin.announcements.index')"
    :paginator="$announcements"
    item-label="announcements"
>
    <x-slot:filterStart>
        <x-dash.app-search
            type="search"
            name="q"
            id="announcements-filter-q"
            :value="$filters['q']"
            placeholder="Search by title…"
        />
        <x-dash.app-search type="select" name="status" id="announcements-filter-status" icon="circle-small">
            <option value="">All statuses</option>
            @foreach (AnnouncementStatus::cases() as $st)
                <option value="{{ $st->value }}" @selected($filters['status']?->value === $st->value)>{{ $st->label() }}</option>
            @endforeach
        </x-dash.app-search>
        <x-dash.app-search type="select" name="scope" id="announcements-filter-scope" icon="circle-small">
            <option value="">All scopes</option>
            @foreach (AnnouncementScope::cases() as $sc)
                <option value="{{ $sc->value }}" @selected($filters['scope']?->value === $sc->value)>{{ $sc->label() }}</option>
            @endforeach
        </x-dash.app-search>
        <x-dash.app-search type="select" name="category" id="announcements-filter-category" icon="circle-small">
            <option value="">All categories</option>
            @foreach (AnnouncementCategory::cases() as $cat)
                <option value="{{ $cat->value }}" @selected($filters['category']?->value === $cat->value)>{{ $cat->label() }}</option>
            @endforeach
        </x-dash.app-search>
        @if ($hasActiveFilters)
            <x-dash.button variant="secondary" :href="platform_route('admin.announcements.index')" data-dash-partial-link>Reset</x-dash.button>
        @endif
    </x-slot:filterStart>

    @if ($hasActiveFilters)
        <x-slot:pills>
            <x-dash.filter-pills
                :pills="$activeFilterPills"
                :reset-url="platform_route('admin.announcements.index')"
            />
        </x-slot:pills>
    @endif

    <x-slot:header>
        <tr class="text-uppercase fs-xxs">
            <th>Title</th>
            <th>Scope</th>
            <th>Category</th>
            <th>Status</th>
            <th>Expires</th>
            <th class="text-end">Actions</th>
        </tr>
    </x-slot:header>

    <x-slot:body>
        @forelse ($announcements as $announcement)
            <tr>
                <td class="fw-medium">{{ $announcement->title }}</td>
                <td class="text-muted fs-sm">
                    {{ $announcement->scope->label() }}
                    @if ($announcement->journal)
                        <span class="d-block">{{ $announcement->journal->name }}</span>
                    @endif
                </td>
                <td class="text-muted fs-sm">{{ $announcement->category->label() }}</td>
                <td>
                    @include('admin.announcements.partials.status-badge', ['status' => $announcement->status])
                </td>
                <td class="text-muted fs-sm">
                    {{ $announcement->expires_at?->format('M j, Y g:i A') ?? '—' }}
                </td>
                <td class="text-end text-nowrap">
                    <x-dash.link :href="platform_route('admin.announcements.edit', $announcement)">Edit</x-dash.link>
                    <span class="text-muted mx-1">·</span>

                    <form
                        id="announcement-delete-form-{{ $announcement->id }}"
                        method="POST"
                        action="{{ platform_route('admin.announcements.destroy', $announcement) }}"
                        class="d-none"
                    >
                        @csrf
                        @method('DELETE')
                    </form>

                    <button
                        type="button"
                        class="btn btn-link p-0 border-0 text-danger"
                        data-admin-confirm-open
                        data-confirm-form="announcement-delete-form-{{ $announcement->id }}"
                        data-confirm-title="Delete announcement?"
                        data-confirm-message="Delete '{{ $announcement->title }}'? This action cannot be undone."
                        data-confirm-label="Delete"
                        data-confirm-variant="danger"
                    >
                        Delete
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="p-0">
                    <x-dash.empty
                        :title="$hasActiveFilters ? 'No announcements found' : 'No announcements yet'"
                        :description="$hasActiveFilters ? 'Try adjusting your search or filters.' : 'Create your first announcement to show on journal home pages.'"
                    >
                        @unless ($hasActiveFilters)
                            <x-dash.button :href="platform_route('admin.announcements.create')">New announcement</x-dash.button>
                        @endunless
                    </x-dash.empty>
                </td>
            </tr>
        @endforelse
    </x-slot:body>
</x-dash.list-card>
