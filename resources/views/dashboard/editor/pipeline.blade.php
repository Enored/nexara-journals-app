@extends('layouts.dashboard', ['activeNav' => 'editor-pipeline'])

@section('title', 'Editorial pipeline')
@section('pageTitle', 'Editorial pipeline')
@section('pageDescription', 'Manage submissions and peer review for your journals.')

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @include('partials.dashboard.stat-card', ['label' => 'All submissions', 'value' => $stats['total']])
        @include('partials.dashboard.stat-card', ['label' => 'In pipeline', 'value' => $stats['pipeline'], 'accent' => 'amber'])
        @include('partials.dashboard.stat-card', ['label' => 'Under review', 'value' => $stats['under_review'], 'accent' => 'sky'])
        @include('partials.dashboard.stat-card', ['label' => 'Accepted', 'value' => $stats['accepted'], 'accent' => 'violet'])
    </div>

    <form method="GET" action="{{ platform_route('editor.pipeline') }}" class="dash-card mt-6 w-full p-4">
        <div class="flex w-full flex-col gap-4 lg:flex-row lg:items-end">
            <div class="min-w-0 w-full flex-[2]">
                <label for="editor-filter-q" class="dash-field-label">Search</label>
                <input
                    type="search"
                    id="editor-filter-q"
                    name="q"
                    value="{{ $filters['q'] }}"
                    placeholder="Title or abstract…"
                    class="dash-input"
                />
            </div>
            <div class="min-w-0 w-full flex-1">
                <label for="editor-filter-journal" class="dash-field-label">Journal</label>
                <select id="editor-filter-journal" name="journal" class="dash-select">
                    <option value="">All my journals</option>
                    @foreach ($editorJournals as $journal)
                        <option value="{{ $journal->subdomain }}" @selected($filters['journal'] === $journal->subdomain)>{{ $journal->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-0 w-full flex-1 lg:max-w-xs">
                <label for="editor-filter-status" class="dash-field-label">Status</label>
                <select id="editor-filter-status" name="status" class="dash-select">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected($filters['status'] === $status)>{{ str_replace('_', ' ', $status->value) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex w-full shrink-0 flex-wrap gap-2 lg:w-auto">
                <x-dash.button type="submit" class="w-full sm:w-auto">Apply</x-dash.button>
                @if ($hasActiveFilters)
                    <x-dash.button variant="secondary" :href="platform_route('editor.pipeline')" class="w-full sm:w-auto">Reset</x-dash.button>
                @endif
            </div>
        </div>
    </form>

    <x-dash.filter-pills
        class="mt-4"
        :pills="$activeFilterPills"
        :reset-url="$hasActiveFilters ? platform_route('editor.pipeline') : null"
    />

    <x-dash.table class="mt-6">
        <x-slot:header>
            <tr>
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
                    <td class="whitespace-nowrap text-slate-600">{{ $submission->submitted_at->format('M j, Y') }}</td>
                    <td class="max-w-xs font-medium text-slate-900">
                        <a href="{{ platform_route('editor.submissions.show', $submission) }}" class="hover:text-teal-700 hover:underline">{{ Str::limit($submission->title, 56) }}</a>
                    </td>
                    <td class="text-slate-600">{{ $submission->author->name }}</td>
                    <td class="text-slate-600">{{ $submission->journal->name }}</td>
                    <td>@include('partials.submission-status', ['status' => $submission->status])</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="!p-0">
                        <x-dash.empty
                            title="No submissions match"
                            :description="$hasActiveFilters ? 'Try adjusting your search or filters.' : 'Check back when authors submit new work.'"
                        />
                    </td>
                </tr>
            @endforelse
        </x-slot:body>
    </x-dash.table>

    <x-dash.pagination :paginator="$submissions" item-label="submissions" />
@endsection
