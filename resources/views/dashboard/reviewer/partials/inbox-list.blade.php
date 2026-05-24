@php
    use App\Support\ReviewerInboxIndexFilters;
@endphp

<x-dash.list-card
    class="mt-4"
    :filter-action="platform_route('reviewer.inbox')"
    :paginator="$assignments"
    item-label="assignments"
>
    <x-slot:filterStart>
        <x-dash.app-search
            type="search"
            name="q"
            id="reviewer-filter-q"
            :value="$filters['q']"
            placeholder="Search manuscript title…"
        />
        <x-dash.app-search type="select" name="journal" id="reviewer-filter-journal" icon="book-open">
            <option value="">All my journals</option>
            @foreach ($reviewerJournals as $j)
                <option value="{{ $j->subdomain }}" @selected($filters['journal'] === $j->subdomain)>{{ $j->name }}</option>
            @endforeach
        </x-dash.app-search>
        <x-dash.app-search type="select" name="status" id="reviewer-filter-status" icon="circle-small">
            <option value="" @selected($filters['status_mode'] === 'active')>Needs attention</option>
            <option value="{{ ReviewerInboxIndexFilters::STATUS_PARAM_ALL }}" @selected($filters['status_mode'] === 'all')>All statuses</option>
            @foreach ($assignmentStatuses as $st)
                <option value="{{ $st->value }}" @selected($filters['status_mode'] === 'single' && $filters['status'] === $st)>{{ str_replace('_', ' ', ucfirst($st->value)) }}</option>
            @endforeach
        </x-dash.app-search>
        <x-dash.app-search type="select" name="sort" id="reviewer-filter-sort" icon="arrow-up-down">
            <option value="{{ ReviewerInboxIndexFilters::SORT_DEADLINE }}" @selected($filters['sort'] === ReviewerInboxIndexFilters::SORT_DEADLINE)>Deadline (soonest)</option>
            <option value="{{ ReviewerInboxIndexFilters::SORT_INVITED }}" @selected($filters['sort'] === ReviewerInboxIndexFilters::SORT_INVITED)>Recently invited</option>
        </x-dash.app-search>
        @if ($hasActiveFilters)
            <x-dash.button variant="secondary" :href="platform_route('reviewer.inbox')" data-dash-partial-link>Reset</x-dash.button>
        @endif
    </x-slot:filterStart>
    @if ($hasActiveFilters)
        <x-slot:pills>
            <x-dash.filter-pills
                :pills="$activeFilterPills"
                :reset-url="platform_route('reviewer.inbox')"
            />
        </x-slot:pills>
    @endif
    <x-slot:header>
        <tr class="text-uppercase fs-xxs">
            <th>Deadline</th>
            <th>Journal</th>
            <th>Manuscript</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
        </tr>
    </x-slot:header>
    <x-slot:body>
        @forelse ($assignments as $a)
            @php($overdue = $a->deadline->isPast() && ! in_array($a->status->value, ['completed', 'declined', 'expired'], true))
            <tr @class(['table-danger' => $overdue])>
                <td class="text-nowrap fw-medium">
                    <span @class(['text-danger' => $overdue])>{{ $a->deadline->format('M j, Y') }}</span>
                    @if ($overdue)
                        <span class="badge badge-soft-danger fs-xxs ms-1">Overdue</span>
                    @endif
                </td>
                <td class="text-muted">{{ $a->submission->journal->name }}</td>
                <td class="fw-medium" style="max-width: 18rem;">
                    <a href="{{ platform_route('review-tasks.show', $a) }}" class="link-reset">{{ Str::limit($a->submission->title, 48) }}</a>
                </td>
                <td>@include('partials.review-assignment-status', ['status' => $a->status])</td>
                <td class="text-end text-nowrap">
                    <a href="{{ platform_route('review-tasks.show', $a) }}" class="link-primary fw-medium fs-sm">View</a>
                    @if ($a->status === \App\Enums\ReviewAssignmentStatus::Invited)
                        <span class="text-muted mx-1">·</span>
                        <form method="POST" action="{{ platform_route('review-tasks.accept', $a) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link btn-sm link-success p-0 fw-medium">Accept</button>
                        </form>
                        <span class="text-muted mx-1">·</span>
                        <form method="POST" action="{{ platform_route('review-tasks.decline', $a) }}" class="d-inline" onsubmit="return confirm('Decline this invitation?');">
                            @csrf
                            <button type="submit" class="btn btn-link btn-sm link-secondary p-0 fw-medium">Decline</button>
                        </form>
                    @elseif ($a->status === \App\Enums\ReviewAssignmentStatus::Accepted && ! $a->review)
                        <span class="text-muted mx-1">·</span>
                        <span class="fw-medium fs-sm">Write review</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="p-0">
                    @if (($stats['total'] ?? 0) === 0)
                        <x-dash.empty
                            title="No review assignments"
                            description="When an editor invites you to review, the task will show up here."
                        />
                    @elseif ($hasActiveFilters)
                        <x-dash.empty
                            title="No assignments match"
                            description="Try adjusting your search or filters."
                        />
                    @else
                        <x-dash.empty
                            title="Nothing needs attention"
                            description='You have no open invitations or reviews in progress. Past assignments stay hidden here - choose "All statuses" or "Completed" to see history.'
                        />
                    @endif
                </td>
            </tr>
        @endforelse
    </x-slot:body>
</x-dash.list-card>
