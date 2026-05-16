@extends('layouts.dashboard', ['activeNav' => 'editor-pipeline'])

@section('title', Str::limit($submission->title, 48))
@section('pageTitle', $submission->title)
@section('pageDescription', $submission->journal->name . ' · ' . $submission->author->name . ' · ' . str_replace('_', ' ', $submission->status->value))

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <x-dash.link :href="platform_route('editor.pipeline')">← Editorial pipeline</x-dash.link>
        <div class="flex flex-wrap items-center gap-2">
            @include('partials.submission-status', ['status' => $submission->status])
            <span class="text-sm text-slate-500">Round {{ $submission->version }}</span>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            @foreach ($timeline['versions'] as $round)
                @include('submissions.partials.editor-version-card', [
                    'submission' => $submission,
                    'round' => $round,
                ])
            @endforeach

            @if ($timeline['published'])
                <section class="dash-card border-violet-200 bg-violet-50/50 p-6">
                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                        <h2 class="text-lg font-semibold text-slate-900">Published</h2>
                        <time class="text-sm text-slate-500" datetime="{{ $timeline['published']['at']->toIso8601String() }}">
                            {{ $timeline['published']['at']->format('M j, Y g:i A') }}
                        </time>
                    </div>
                    <p class="mt-3 text-sm text-slate-700">
                        <a href="{{ $timeline['published']['url'] }}" class="font-medium text-teal-700 hover:underline" target="_blank" rel="noopener">Open public article page</a>
                        <span class="text-slate-500">({{ $submission->journal->subdomain }}.{{ config('journal.base_domain') }})</span>
                    </p>
                </section>
            @endif

            @can('assignReviewer', $submission)
                <section class="dash-card border-dashed border-slate-300 p-6">
                    <h2 class="text-lg font-semibold text-slate-900">Assign reviewer</h2>
                    <p class="mt-1 text-sm text-slate-600">
                        Invitation applies to <strong>version {{ $submission->version }}</strong>. Users must have the reviewer role on this journal.
                    </p>
                    <form method="POST" action="{{ platform_route('editor.submissions.assign-reviewer', $submission) }}" class="mt-4 flex flex-wrap items-end gap-3">
                        @csrf
                        <div class="min-w-[14rem] flex-1">
                            <label class="dash-field-label">Reviewer</label>
                            <select name="reviewer_id" required class="dash-select mt-1 w-full">
                                <option value="">Select…</option>
                                @foreach ($reviewerPool as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="dash-field-label">Deadline</label>
                            <input type="date" name="deadline" required class="dash-input mt-1" min="{{ now()->addDay()->toDateString() }}">
                        </div>
                        <x-dash.button type="submit">Send invitation</x-dash.button>
                    </form>
                </section>
            @endcan

            @can('recordDecision', $submission)
                @if ($submission->status === \App\Enums\SubmissionStatus::UnderReview)
                    <section class="dash-card p-6">
                        <h2 class="text-lg font-semibold text-slate-900">Editorial decision</h2>
                        <p class="mt-1 text-sm text-slate-600">Record the outcome for <strong>version {{ $submission->version }}</strong> and the letter to the author.</p>
                        <form method="POST" action="{{ platform_route('editor.submissions.decision', $submission) }}" class="mt-4 space-y-4">
                            @csrf
                            <div>
                                <label class="dash-field-label">Decision</label>
                                <select name="decision" required class="dash-select mt-1 w-full max-w-md">
                                    <option value="accept">Accept</option>
                                    <option value="minor_revision">Request minor revision</option>
                                    <option value="major_revision">Request major revision</option>
                                    <option value="reject">Reject</option>
                                </select>
                            </div>
                            <x-dash.textarea name="decision_letter" label="Decision letter" rows="8" required>{{ old('decision_letter') }}</x-dash.textarea>
                            <x-dash.button type="submit" variant="secondary" class="!bg-slate-900 !text-white hover:!bg-slate-800">Send decision</x-dash.button>
                        </form>
                    </section>
                @endif
            @endcan
        </div>

        <div class="space-y-6">
            <section class="dash-card p-6 text-sm">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Author</h2>
                <p class="mt-2 font-medium text-slate-900">{{ $submission->author->name }}</p>
                <p class="text-slate-600">{{ $submission->author->email }}</p>
                <p class="mt-3 text-xs text-slate-500">Article type: {{ $submission->article_type }}</p>
            </section>

            <section class="dash-card p-6">
                <h2 class="text-lg font-semibold text-slate-900">Peer review</h2>
                <p class="mt-1 text-sm text-slate-600">All invitations and assignments for this manuscript.</p>
                <div class="mt-4 overflow-x-auto">
                    <table class="dash-table text-sm">
                        <thead>
                            <tr>
                                <th>Round</th>
                                <th>Reviewer</th>
                                <th>Deadline</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($submission->reviewAssignments->sortByDesc('invited_at') as $ra)
                                <tr>
                                    <td class="text-slate-600">v{{ $ra->round_version ?? 1 }}</td>
                                    <td>{{ $ra->reviewer->name }}</td>
                                    <td class="whitespace-nowrap text-slate-600">{{ $ra->deadline->format('M j, Y') }}</td>
                                    <td>@include('partials.review-assignment-status', ['status' => $ra->status])</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-slate-500">No reviewers assigned yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            @can('publish', $submission)
                <section class="dash-card border-emerald-200 bg-emerald-50/50 p-6">
                    <h2 class="text-lg font-semibold text-slate-900">Publish in an issue</h2>
                    <p class="mt-1 text-sm text-slate-600">Assign this accepted manuscript to a journal issue.</p>
                    <p class="mt-2 text-sm">
                        <a href="{{ journal_front_url($submission->journal, '/editions') }}" class="font-medium text-teal-700 hover:underline">Manage issues</a>
                        ·
                        <a href="{{ journal_front_url($submission->journal, '/editions/create') }}" class="font-medium text-teal-700 hover:underline">New issue</a>
                    </p>
                    @if ($editionsForPublish->isEmpty())
                        <p class="mt-3 text-sm text-amber-900">No issues exist yet. Create at least one issue before publishing.</p>
                    @else
                        <form method="POST" action="{{ platform_route('editor.submissions.publish', $submission) }}" class="mt-4 space-y-3">
                            @csrf
                            <div>
                                <label class="dash-field-label">Issue</label>
                                <select name="edition_id" required class="dash-select mt-1 w-full">
                                    @foreach ($editionsForPublish as $ed)
                                        <option value="{{ $ed->id }}">Vol. {{ $ed->volume }}, No. {{ $ed->issue }}@if ($ed->title) — {{ $ed->title }}@endif</option>
                                    @endforeach
                                </select>
                            </div>
                            <x-dash.button type="submit" class="bg-emerald-700 hover:bg-emerald-800">Publish</x-dash.button>
                        </form>
                    @endif
                </section>
            @endcan
        </div>
    </div>
@endsection
