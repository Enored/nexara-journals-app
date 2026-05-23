@extends('layouts.dashboard', ['activeNav' => 'reviewer-inbox'])

@php
    use App\Enums\ReviewAssignmentStatus;
    $round = $timeline['versions'][0] ?? null;
    $overdue = $assignment->deadline->isPast() && ! in_array($assignment->status->value, ['completed', 'declined', 'expired'], true);
@endphp

@section('title', Str::limit($submission->title, 48))
@section('pageTitle', $submission->title)
@section('pageDescription', $submission->journal->name . ' · Review round ' . ($assignment->round_version ?? 1))

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <x-dash.link :href="platform_route('reviewer.inbox')">← Review inbox</x-dash.link>
        <div class="flex flex-wrap items-center gap-2">
            @include('partials.review-assignment-status', ['status' => $assignment->status])
            <span @class(['text-sm', 'font-medium text-rose-700' => $overdue, 'text-slate-500' => ! $overdue])>
                Deadline {{ $assignment->deadline->format('M j, Y') }}
                @if ($overdue)
                    · Overdue
                @endif
            </span>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            @if ($round)
                @include('submissions.partials.editor-version-card', [
                    'submission' => $submission,
                    'round' => $round,
                    'forAuthor' => false,
                ])
            @endif

            @if ($assignment->status === ReviewAssignmentStatus::Invited)
                <section class="dash-card border-amber-200 bg-amber-50/60 p-6">
                    <h2 class="text-lg font-semibold text-slate-900">Review invitation</h2>
                    <p class="mt-1 text-sm text-slate-600">Accept to access the manuscript and submit your review by <strong>{{ $assignment->deadline->format('M j, Y') }}</strong>.</p>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <form method="POST" action="{{ platform_route('review-tasks.accept', $assignment) }}">
                            @csrf
                            <x-dash.button type="submit" class="!bg-emerald-700 hover:!bg-emerald-800">Accept invitation</x-dash.button>
                        </form>
                        <form method="POST" action="{{ platform_route('review-tasks.decline', $assignment) }}" class="flex flex-wrap items-end gap-2">
                            @csrf
                            <div class="min-w-[14rem] flex-1">
                                <label for="decline-reason" class="dash-field-label">Reason (optional)</label>
                                <input id="decline-reason" type="text" name="reason" value="{{ old('reason') }}" placeholder="Optional reason" class="dash-input mt-1 w-full">
                            </div>
                            <x-dash.button type="submit" variant="secondary">Decline</x-dash.button>
                        </form>
                    </div>
                </section>
            @elseif ($assignment->status === ReviewAssignmentStatus::Accepted && ! $assignment->review)
                <section class="dash-card p-6">
                    <h2 class="text-lg font-semibold text-slate-900">Submit your review</h2>
                    <p class="mt-1 text-sm text-slate-600">Your report is <strong>confidential to the editor</strong>. The author will not see it directly; the editor may quote selected points in their decision letter.</p>
                    <form method="POST" action="{{ platform_route('review-tasks.store', $assignment) }}" class="mt-4 space-y-4">
                        @csrf
                        <div class="grid gap-4 sm:grid-cols-3">
                            <x-dash.input name="originality_score" type="number" label="Originality (1–5)" :value="old('originality_score')" min="1" max="5" required />
                            <x-dash.input name="methodology_score" type="number" label="Methodology (1–5)" :value="old('methodology_score')" min="1" max="5" required />
                            <x-dash.input name="clarity_score" type="number" label="Clarity (1–5)" :value="old('clarity_score')" min="1" max="5" required />
                        </div>
                        <div class="dash-field">
                            <label for="recommendation" class="dash-field-label">Recommendation</label>
                            <select id="recommendation" name="recommendation" required class="dash-select mt-1 w-full max-w-md">
                                <option value="accept" @selected(old('recommendation') === 'accept')>Accept</option>
                                <option value="minor_revision" @selected(old('recommendation') === 'minor_revision')>Minor revision</option>
                                <option value="major_revision" @selected(old('recommendation') === 'major_revision')>Major revision</option>
                                <option value="reject" @selected(old('recommendation') === 'reject')>Reject</option>
                            </select>
                        </div>
                        <x-dash.textarea name="comments_for_editor" label="Confidential comments to the editor" rows="8" required>{{ old('comments_for_editor') }}</x-dash.textarea>
                        <x-dash.button type="submit">Submit review</x-dash.button>
                    </form>
                </section>
            @elseif ($assignment->review)
                <section class="dash-card border-slate-200 bg-slate-50/80 p-6 text-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Review submitted</h2>
                    <p class="mt-2 text-slate-600">Your review is on file. See the activity timeline on the version card for details.</p>
                </section>
            @else
                <section class="dash-card p-6 text-sm text-slate-600">
                    This task is <strong>{{ str_replace('_', ' ', $assignment->status->value) }}</strong> and cannot be edited here.
                </section>
            @endif
        </div>

        <div class="space-y-6">
            <section class="dash-card p-6 text-sm">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Assignment</h2>
                <dl class="mt-3 space-y-3">
                    <div>
                        <dt class="text-xs text-slate-500">Journal</dt>
                        <dd class="font-medium text-slate-900">{{ $submission->journal->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">Review round</dt>
                        <dd class="text-slate-800">Version {{ $assignment->round_version ?? 1 }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">Deadline</dt>
                        <dd @class(['font-medium', 'text-rose-700' => $overdue, 'text-slate-800' => ! $overdue])>{{ $assignment->deadline->format('M j, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">Article type</dt>
                        <dd class="text-slate-800">{{ $submission->article_type }}</dd>
                    </div>
                </dl>
                <p class="mt-4">
                    <a href="{{ journal_front_url($submission->journal, '/') }}" class="font-medium text-teal-700 hover:underline">Visit journal site</a>
                </p>
            </section>
        </div>
    </div>
@endsection
