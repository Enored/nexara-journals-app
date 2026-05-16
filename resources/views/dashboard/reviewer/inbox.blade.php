@extends('layouts.dashboard', ['activeNav' => 'reviewer-inbox'])

@php
    use App\Support\ReviewerInboxIndexFilters;
@endphp

@section('title', 'Review inbox')
@section('pageTitle', 'Review inbox')
@section('pageDescription', 'Invitations and reviews that need your attention. Completed work stays out of the default list.')

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @include('partials.dashboard.stat-card', ['label' => 'Invitations', 'value' => $stats['invited'], 'accent' => 'amber'])
        @include('partials.dashboard.stat-card', ['label' => 'In progress', 'value' => $stats['active'], 'accent' => 'sky'])
        @include('partials.dashboard.stat-card', ['label' => 'Completed', 'value' => $stats['completed'], 'accent' => 'violet'])
        @include('partials.dashboard.stat-card', ['label' => 'Overdue', 'value' => $stats['overdue'], 'accent' => 'rose'])
    </div>

    <form method="GET" action="{{ platform_route('reviewer.inbox') }}" class="dash-card mt-6 w-full p-4">
        <div class="flex w-full flex-col gap-4 lg:flex-row lg:items-end">
            <div class="min-w-0 w-full flex-[2]">
                <label for="reviewer-filter-q" class="dash-field-label">Title</label>
                <input
                    type="search"
                    id="reviewer-filter-q"
                    name="q"
                    value="{{ $filters['q'] }}"
                    placeholder="Manuscript title…"
                    class="dash-input"
                />
            </div>
            <div class="min-w-0 w-full flex-1">
                <label for="reviewer-filter-journal" class="dash-field-label">Journal</label>
                <select id="reviewer-filter-journal" name="journal" class="dash-select">
                    <option value="">All my journals</option>
                    @foreach ($reviewerJournals as $j)
                        <option value="{{ $j->subdomain }}" @selected($filters['journal'] === $j->subdomain)>{{ $j->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-0 w-full flex-1 lg:max-w-xs">
                <label for="reviewer-filter-status" class="dash-field-label">Status</label>
                <select id="reviewer-filter-status" name="status" class="dash-select">
                    <option value="" @selected($filters['status_mode'] === 'active')>Needs attention</option>
                    <option value="{{ ReviewerInboxIndexFilters::STATUS_PARAM_ALL }}" @selected($filters['status_mode'] === 'all')>All statuses</option>
                    @foreach ($assignmentStatuses as $st)
                        <option value="{{ $st->value }}" @selected($filters['status_mode'] === 'single' && $filters['status'] === $st)>{{ str_replace('_', ' ', ucfirst($st->value)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-0 w-full flex-1 lg:max-w-xs">
                <label for="reviewer-filter-sort" class="dash-field-label">Sort by</label>
                <select id="reviewer-filter-sort" name="sort" class="dash-select">
                    <option value="{{ ReviewerInboxIndexFilters::SORT_DEADLINE }}" @selected($filters['sort'] === ReviewerInboxIndexFilters::SORT_DEADLINE)>Deadline (soonest)</option>
                    <option value="{{ ReviewerInboxIndexFilters::SORT_INVITED }}" @selected($filters['sort'] === ReviewerInboxIndexFilters::SORT_INVITED)>Recently invited</option>
                </select>
            </div>
            <div class="flex w-full shrink-0 flex-wrap gap-2 lg:w-auto">
                <x-dash.button type="submit" class="w-full sm:w-auto">Apply</x-dash.button>
                @if ($hasActiveFilters)
                    <x-dash.button variant="secondary" :href="platform_route('reviewer.inbox')" class="w-full sm:w-auto">Reset</x-dash.button>
                @endif
            </div>
        </div>
    </form>

    <x-dash.filter-pills
        class="mt-4"
        :pills="$activeFilterPills"
        :reset-url="$hasActiveFilters ? platform_route('reviewer.inbox') : null"
    />

    <x-dash.table class="mt-6">
        <x-slot:header>
            <tr>
                <th>Deadline</th>
                <th>Journal</th>
                <th>Manuscript</th>
                <th>Status</th>
                <th class="text-right">Actions</th>
            </tr>
        </x-slot:header>
        <x-slot:body>
            @forelse ($assignments as $a)
                @php($overdue = $a->deadline->isPast() && ! in_array($a->status->value, ['completed', 'declined', 'expired'], true))
                <tr @class(['bg-rose-50/60' => $overdue])>
                    <td class="whitespace-nowrap font-medium {{ $overdue ? 'text-rose-800' : 'text-slate-700' }}">
                        {{ $a->deadline->format('M j, Y') }}
                        @if ($overdue)
                            <span class="ml-1 text-xs font-semibold text-rose-600">Overdue</span>
                        @endif
                    </td>
                    <td class="text-slate-600">{{ $a->submission->journal->name }}</td>
                    <td class="max-w-xs font-medium text-slate-900">
                        <a href="{{ platform_route('review-tasks.show', $a) }}" class="hover:text-teal-700 hover:underline">{{ Str::limit($a->submission->title, 48) }}</a>
                    </td>
                    <td>@include('partials.review-assignment-status', ['status' => $a->status])</td>
                    <td class="whitespace-nowrap text-right text-sm">
                        <a href="{{ platform_route('review-tasks.show', $a) }}" class="font-medium text-teal-700 hover:underline">View</a>
                        @if ($a->status === \App\Enums\ReviewAssignmentStatus::Invited)
                            <span class="text-slate-300">·</span>
                            <form method="POST" action="{{ platform_route('review-tasks.accept', $a) }}" class="inline">@csrf<button class="font-medium text-emerald-700 hover:underline">Accept</button></form>
                            <span class="text-slate-300">·</span>
                            <form method="POST" action="{{ platform_route('review-tasks.decline', $a) }}" class="inline" onsubmit="return confirm('Decline this invitation?');">@csrf<button class="font-medium text-slate-600 hover:underline">Decline</button></form>
                        @elseif ($a->status === \App\Enums\ReviewAssignmentStatus::Accepted && ! $a->review)
                            <span class="text-slate-300">·</span>
                            <span class="font-medium text-slate-700">Write review</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="!p-0">
                        @if (($stats['total'] ?? 0) === 0)
                            <x-dash.empty
                                title="No review assignments"
                                description="When an editor invites you to review, the task will show up here."
                            />
                        @else
                            @if ($hasActiveFilters)
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
                        @endif
                    </td>
                </tr>
            @endforelse
        </x-slot:body>
    </x-dash.table>

    <x-dash.pagination :paginator="$assignments" item-label="assignments" />
@endsection
