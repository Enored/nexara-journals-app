<x-dash.list-card
    class="mt-4"
    :filter-action="platform_route('editor.submissions')"
    :paginator="$submissions"
    item-label="submissions"
>
    <x-slot:filterStart>
        <x-dash.app-search
            type="search"
            name="q"
            id="editor-filter-q"
            :value="$filters['q']"
            placeholder="Search title or abstract…"
        />
        <x-dash.app-search type="select" name="journal" id="editor-filter-journal" icon="book-open">
            <option value="">All my journals</option>
            @foreach ($editorJournals as $journal)
                <option value="{{ $journal->subdomain }}" @selected($filters['journal'] === $journal->subdomain)>{{ $journal->name }}</option>
            @endforeach
        </x-dash.app-search>
        <x-dash.app-search type="select" name="status" id="editor-filter-status" icon="circle-small">
            <option value="">All statuses</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected($filters['status'] === $status)>{{ str_replace('_', ' ', $status->value) }}</option>
            @endforeach
        </x-dash.app-search>
        @if ($hasActiveFilters)
            <x-dash.button variant="secondary" :href="platform_route('editor.submissions')" data-dash-partial-link>Reset</x-dash.button>
        @endif
    </x-slot:filterStart>
    @if ($hasActiveFilters)
        <x-slot:pills>
            <x-dash.filter-pills
                :pills="$activeFilterPills"
                :reset-url="platform_route('editor.submissions')"
            />
        </x-slot:pills>
    @endif
    <x-slot:header>
        <tr class="text-uppercase fs-xxs">
            <th>Submitted</th>
            <th>Title</th>
            <th>Author</th>
            <th>Journal</th>
            <th>Status</th>
        </tr>
    </x-slot:header>
    <x-slot:body>
        @forelse ($submissions as $submission)
            <tr>
                <td class="text-muted text-nowrap">{{ $submission->submitted_at->format('M j, Y') }}</td>
                <td class="fw-medium" style="max-width: 18rem;">
                    <a href="{{ platform_route('editor.submissions.show', $submission) }}" class="link-reset">{{ Str::limit($submission->title, 56) }}</a>
                </td>
                <td class="text-muted">{{ $submission->author->name }}</td>
                <td class="text-muted">{{ $submission->journal->name }}</td>
                <td>@include('partials.submission-status', ['status' => $submission->status])</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="p-0">
                    <x-dash.empty
                        title="No submissions match"
                        :description="$hasActiveFilters ? 'Try adjusting your search or filters.' : 'Check back when authors submit new work.'"
                    />
                </td>
            </tr>
        @endforelse
    </x-slot:body>
</x-dash.list-card>
