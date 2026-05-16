@extends('layouts.dashboard', ['activeNav' => 'author-submissions'])

@section('title', 'My submissions')
@section('pageTitle', 'My submissions')
@section('pageDescription', 'Track manuscripts and respond to revision requests.')

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @include('partials.dashboard.stat-card', ['label' => 'Total', 'value' => $stats['total']])
        @include('partials.dashboard.stat-card', ['label' => 'Active', 'value' => $stats['active'], 'hint' => 'In editorial workflow', 'accent' => 'sky'])
        @include('partials.dashboard.stat-card', ['label' => 'Revision due', 'value' => $stats['revision'], 'accent' => 'amber'])
        @include('partials.dashboard.stat-card', ['label' => 'Published', 'value' => $stats['published'], 'accent' => 'violet'])
    </div>

    <div class="mt-6 rounded-xl border border-slate-200 bg-white p-4 text-sm text-slate-600 shadow-sm">
        <strong class="font-medium text-slate-900">New submission:</strong>
        visit a journal site at
        <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">https://{subdomain}.{{ config('journal.base_domain') }}</code>
        and use <em>Submit manuscript</em> while signed in.
    </div>

    <form method="GET" action="{{ platform_route('author.submissions') }}" class="dash-card mt-6 w-full p-4">
        <div class="flex w-full flex-col gap-4 lg:flex-row lg:items-end">
            <div class="min-w-0 w-full flex-[2]">
                <label for="author-filter-q" class="dash-field-label">Title</label>
                <input
                    type="search"
                    id="author-filter-q"
                    name="q"
                    value="{{ $filters['q'] }}"
                    placeholder="Title…"
                    class="dash-input"
                />
            </div>
            <div class="min-w-0 w-full flex-1">
                <label for="author-filter-journal" class="dash-field-label">Journal</label>
                <select id="author-filter-journal" name="journal" class="dash-select">
                    <option value="">All my journals</option>
                    @foreach ($authorJournals as $journal)
                        <option value="{{ $journal->subdomain }}" @selected($filters['journal'] === $journal->subdomain)>{{ $journal->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-0 w-full flex-1 lg:max-w-xs">
                <label for="author-filter-status" class="dash-field-label">Status</label>
                <select id="author-filter-status" name="status" class="dash-select">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected($filters['status'] === $status)>{{ str_replace('_', ' ', $status->value) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex w-full shrink-0 flex-wrap gap-2 lg:w-auto">
                <x-dash.button type="submit" class="w-full sm:w-auto">Apply</x-dash.button>
                @if ($hasActiveFilters)
                    <x-dash.button variant="secondary" :href="platform_route('author.submissions')" class="w-full sm:w-auto">Reset</x-dash.button>
                @endif
            </div>
        </div>
    </form>

    <x-dash.filter-pills
        class="mt-4"
        :pills="$activeFilterPills"
        :reset-url="$hasActiveFilters ? platform_route('author.submissions') : null"
    />

    <x-dash.table class="mt-6">
        <x-slot:header>
            <tr>
                <th>Submitted</th>
                <th>Title</th>
                <th>Journal</th>
                <th>Ver.</th>
                <th>Status</th>
                <th class="text-right"></th>
            </tr>
        </x-slot:header>
        <x-slot:body>
            @forelse ($submissions as $s)
                <tr>
                    <td class="whitespace-nowrap text-slate-600">{{ $s->submitted_at?->format('M j, Y') ?? '—' }}</td>
                    <td class="max-w-xs font-medium text-slate-900">
                        <a href="{{ platform_route('author.submissions.show', $s) }}" class="hover:text-teal-700 hover:underline">{{ Str::limit($s->title, 56) }}</a>
                    </td>
                    <td class="text-slate-600">{{ $s->journal->name }}</td>
                    <td class="text-slate-600">{{ $s->version }}</td>
                    <td>@include('partials.submission-status', ['status' => $s->status])</td>
                    <td class="text-right">
                        <a href="{{ platform_route('author.submissions.show', $s) }}" class="text-sm font-medium text-teal-700 hover:underline">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="!p-0">
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
    </x-dash.table>

    <x-dash.pagination :paginator="$submissions" item-label="submissions" />
@endsection
