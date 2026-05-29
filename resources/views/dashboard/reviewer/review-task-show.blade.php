@extends('layouts.dashboard', ['activeNav' => 'reviewer-inbox'])

@php
    use App\Enums\ReviewAssignmentStatus;
    $round = $timeline['versions'][0] ?? null;
    $overdue = $assignment->deadline->isPast() && ! in_array($assignment->status->value, ['completed', 'expired'], true);
@endphp

@section('title', Str::limit($submission->title, 48))
@section('pageTitle', $submission->title)
@section('pageDescription', $submission->journal->name . ' · Review round ' . ($assignment->round_version ?? 1))

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <x-dash.link :href="platform_route('reviewer.inbox')">← Review inbox</x-dash.link>
        <div class="d-flex flex-wrap align-items-center gap-2">
            @include('partials.review-assignment-status', ['status' => $assignment->status])
            <span class="fs-sm {{ $overdue ? 'fw-medium text-danger' : 'text-muted' }}">
                Deadline {{ $assignment->deadline->format('M j, Y') }}
                @if ($overdue)
                    · Overdue
                @endif
            </span>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            @if ($round)
                @include('submissions.partials.editor-version-card', [
                    'submission' => $submission,
                    'round' => $round,
                    'forAuthor' => false,
                ])
            @endif

            @if ($assignment->status === ReviewAssignmentStatus::Assigned && ! $assignment->review)
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Submit your review</h5>
                        <p class="text-muted fs-sm">Your report is <strong>confidential to the editor</strong>. The author will not see it directly; the editor may quote selected points in their decision letter.</p>
                        <form method="POST" action="{{ platform_route('review-tasks.store', $assignment) }}" class="mt-3">
                            @csrf
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fs-sm fw-medium">Originality (1–5)</label>
                                    <input type="number" name="originality_score" class="form-control" value="{{ old('originality_score') }}" min="1" max="5" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-sm fw-medium">Methodology (1–5)</label>
                                    <input type="number" name="methodology_score" class="form-control" value="{{ old('methodology_score') }}" min="1" max="5" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-sm fw-medium">Clarity (1–5)</label>
                                    <input type="number" name="clarity_score" class="form-control" value="{{ old('clarity_score') }}" min="1" max="5" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="recommendation" class="form-label fs-sm fw-medium">Recommendation</label>
                                <select id="recommendation" name="recommendation" required class="form-select" style="max-width: 20rem;">
                                    <option value="accept" @selected(old('recommendation') === 'accept')>Accept</option>
                                    <option value="minor_revision" @selected(old('recommendation') === 'minor_revision')>Minor revision</option>
                                    <option value="major_revision" @selected(old('recommendation') === 'major_revision')>Major revision</option>
                                    <option value="reject" @selected(old('recommendation') === 'reject')>Reject</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="comments_for_editor" class="form-label fs-sm fw-medium">Confidential comments to the editor</label>
                                <textarea id="comments_for_editor" name="comments_for_editor" rows="8" required class="form-control">{{ old('comments_for_editor') }}</textarea>
                            </div>
                            <x-dash.button type="submit">Submit review</x-dash.button>
                        </form>
                    </div>
                </div>
            @elseif ($assignment->review)
                <div class="card mb-3 bg-light">
                    <div class="card-body">
                        <h5 class="card-title">Review submitted</h5>
                        <p class="text-muted fs-sm mb-0">Your review is on file. The editor will use it when making their decision.</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted fw-semibold fs-xxs mb-2">Assignment</h6>
                    <dl class="mb-0">
                        <dt class="text-muted fs-xs">Journal</dt>
                        <dd class="fw-medium">{{ $submission->journal->name }}</dd>
                        <dt class="text-muted fs-xs">Review round</dt>
                        <dd>Version {{ $assignment->round_version ?? 1 }}</dd>
                        <dt class="text-muted fs-xs">Deadline</dt>
                        <dd class="{{ $overdue ? 'fw-medium text-danger' : '' }}">{{ $assignment->deadline->format('M j, Y') }}</dd>
                        <dt class="text-muted fs-xs">Article type</dt>
                        <dd>{{ $submission->article_type }}</dd>
                    </dl>
                    <a href="{{ journal_front_url($submission->journal, '/') }}" class="fw-medium text-primary fs-sm">Visit journal site</a>
                </div>
            </div>
        </div>
    </div>
@endsection
