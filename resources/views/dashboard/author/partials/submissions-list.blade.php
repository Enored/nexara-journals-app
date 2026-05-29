<x-dash.list-card
    class="mt-4"
    :filter-action="platform_route('author.submissions')"
    :paginator="$submissions"
    item-label="submissions"
>
    <x-slot:filterEnd>
        @if ($submitJournals->isNotEmpty())
            <x-dash.button data-bs-toggle="modal" data-bs-target="#manuscript-create-modal">
                <i data-lucide="file-plus" class="fs-sm me-1"></i>
                Create manuscript
            </x-dash.button>
        @endif
    </x-slot:filterEnd>
    <x-slot:filterStart>
        <x-dash.app-search
            type="search"
            name="q"
            id="author-filter-q"
            :value="$filters['q']"
            placeholder="Search by title…"
        />
        <x-dash.app-search type="select" name="journal" id="author-filter-journal" icon="book-open">
            <option value="">All my journals</option>
            @foreach ($authorJournals as $journal)
                <option value="{{ $journal->subdomain }}" @selected($filters['journal'] === $journal->subdomain)>{{ $journal->name }}</option>
            @endforeach
        </x-dash.app-search>
        <x-dash.app-search type="select" name="status" id="author-filter-status" icon="circle-small">
            <option value="">All statuses</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected($filters['status'] === $status)>{{ str_replace('_', ' ', $status->value) }}</option>
            @endforeach
        </x-dash.app-search>
        @if ($hasActiveFilters)
            <x-dash.button variant="secondary" :href="platform_route('author.submissions')" data-dash-partial-link>Reset</x-dash.button>
        @endif
    </x-slot:filterStart>
    @if ($hasActiveFilters)
        <x-slot:pills>
            <x-dash.filter-pills
                :pills="$activeFilterPills"
                :reset-url="platform_route('author.submissions')"
            />
        </x-slot:pills>
    @endif
    <x-slot:header>
        <tr class="text-uppercase fs-xxs">
            <th>Submitted</th>
            <th>Title</th>
            <th>Journal</th>
            <th>Ver.</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
        </tr>
    </x-slot:header>
    <x-slot:body>
        @forelse ($submissions as $s)
            <tr>
                <td class="text-muted text-nowrap">{{ $s->submitted_at?->format('M j, Y') ?? '—' }}</td>
                <td class="fw-medium" style="max-width: 18rem;">
                    <a href="{{ platform_route('author.submissions.show', $s) }}" class="link-reset">{{ Str::limit($s->title, 56) }}</a>
                </td>
                <td class="text-muted">{{ $s->journal->name }}</td>
                <td class="text-muted">{{ $s->version }}</td>
                <td>@include('partials.submission-status', ['status' => $s->status])</td>
                <td class="text-end">
                    <a href="{{ platform_route('author.submissions.show', $s) }}" class="link-primary fw-medium fs-sm">View</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="p-0">
                    @if ($stats['total'] === 0)
                        <x-dash.empty
                            title="No manuscripts yet"
                            description="When you submit to a journal, your work will appear here with live status updates."
                        />
                    @else
                        <x-dash.empty
                            title="No submissions match"
                            :description="$hasActiveFilters ? 'Try adjusting your search or filters.' : 'Nothing on this page.'"
                        />
                    @endif
                </td>
            </tr>
        @endforelse
    </x-slot:body>
</x-dash.list-card>
