@php($editionCreateModalUrl = platform_route('journal.editions.create', $journal).'?modal=1')

<p class="text-muted mb-4">
    Create <strong>draft</strong> issues, add accepted articles, then <strong>publish</strong> when ready — similar to scheduling content before it goes public.
</p>

<x-dash.list-card
    :filter-action="platform_route('journal.editions.index', $journal)"
    :paginator="$editions"
    item-label="issues"
>
    <x-slot:filterStart>
        <x-dash.app-search
            type="search"
            name="q"
            id="editions-filter-q"
            :value="$filters['q']"
            placeholder="Search title, volume, or issue…"
        />
        <x-dash.app-search type="select" name="status" id="editions-filter-status" icon="circle-small">
            <option value="">All statuses</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected($filters['status'] === $status)>{{ ucfirst($status->value) }}</option>
            @endforeach
        </x-dash.app-search>
        @if ($hasActiveFilters)
            <x-dash.button variant="secondary" :href="platform_route('journal.editions.index', $journal)" data-dash-partial-link>Reset</x-dash.button>
        @endif
    </x-slot:filterStart>
    <x-slot:filterEnd>
        <x-dash.button
            type="button"
            data-edition-create-open
            data-url="{{ $editionCreateModalUrl }}"
            data-journal-name="{{ $journal->name }}"
        >
            <i data-lucide="plus" class="fs-sm me-1"></i>
            New issue
        </x-dash.button>
    </x-slot:filterEnd>
    @if ($hasActiveFilters)
        <x-slot:pills>
            <x-dash.filter-pills
                :pills="$activeFilterPills"
                :reset-url="platform_route('journal.editions.index', $journal)"
            />
        </x-slot:pills>
    @endif
    <x-slot:header>
        <tr class="text-uppercase fs-xxs">
            <th>Volume / Issue</th>
            <th>Title</th>
            <th>Status</th>
            <th>Published</th>
            <th class="text-end">Articles</th>
            <th class="text-end">Actions</th>
        </tr>
    </x-slot:header>
    <x-slot:body>
        @forelse ($editions as $e)
            <tr>
                <td class="fw-medium">{{ $e->volume?->number ?? '—' }} / {{ $e->issue }}</td>
                <td class="text-muted">{{ $e->title ?? '—' }}</td>
                <td>@include('partials.edition-status', ['status' => $e->status])</td>
                <td class="text-muted">{{ $e->isPublished() ? $e->published_at?->format('Y-m-d') : '—' }}</td>
                <td class="text-end">
                    <span class="fw-medium tabular-nums">{{ (int) ($e->slotted_count ?? 0) + (int) ($e->live_count ?? 0) }}</span>
                    @if ((int) ($e->slotted_count ?? 0) + (int) ($e->live_count ?? 0) > 0)
                        <span class="d-block fs-xs text-muted">
                            @if ((int) ($e->slotted_count ?? 0) > 0 && (int) ($e->live_count ?? 0) > 0)
                                {{ (int) $e->slotted_count }} slotted · {{ (int) $e->live_count }} live
                            @elseif ((int) ($e->live_count ?? 0) > 0)
                                {{ (int) $e->live_count }} live
                            @else
                                {{ (int) $e->slotted_count }} slotted
                            @endif
                        </span>
                    @endif
                </td>
                <td class="text-end text-nowrap">
                    <x-dash.link :href="platform_route('journal.editions.show', [$journal, $e])">Manage</x-dash.link>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="p-0">
                    <x-dash.empty
                        :title="$hasActiveFilters ? 'No issues match' : 'No issues yet'"
                        :description="$hasActiveFilters ? 'Try adjusting your search or status filter.' : 'Create a draft issue, add accepted articles, then publish when ready.'"
                    >
                        @if (! $hasActiveFilters)
                            <x-dash.button
                                type="button"
                                data-edition-create-open
                                data-url="{{ $editionCreateModalUrl }}"
                                data-journal-name="{{ $journal->name }}"
                            >New issue</x-dash.button>
                        @endif
                    </x-dash.empty>
                </td>
            </tr>
        @endforelse
    </x-slot:body>
</x-dash.list-card>
