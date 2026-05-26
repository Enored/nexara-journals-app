@extends('layouts.dashboard', ['activeNav' => 'editor-submissions'])

@section('title', Str::limit($submission->title, 48))
@section('pageTitle', $submission->title)
@section('pageDescription', $submission->journal->name . ' · ' . $submission->author->name . ' · ' . str_replace('_', ' ', $submission->status->value))

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <x-dash.link :href="platform_route('editor.submissions')">← Submissions</x-dash.link>
        <div class="d-flex flex-wrap align-items-center gap-2">
            @include('partials.submission-status', ['status' => $submission->status])
            <span class="text-muted fs-sm">Round {{ $submission->version }}</span>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            @foreach ($timeline['versions'] as $round)
                @include('submissions.partials.editor-version-card', [
                    'submission' => $submission,
                    'round' => $round,
                ])
            @endforeach

            @if ($timeline['published'])
                <div class="card border-0 shadow-sm mb-3" style="background: #f3f0ff;">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-baseline justify-content-between gap-2">
                            <h5 class="card-title mb-0">Published</h5>
                            <time class="text-muted fs-sm" datetime="{{ $timeline['published']['at']->toIso8601String() }}">
                                {{ $timeline['published']['at']->format('M j, Y g:i A') }}
                            </time>
                        </div>
                        <p class="mt-3 mb-0 fs-sm">
                            <a href="{{ $timeline['published']['url'] }}" class="fw-medium text-success" target="_blank" rel="noopener">Open public article page</a>
                            <span class="text-muted">({{ $submission->journal->subdomain }}.{{ config('journal.base_domain') }})</span>
                        </p>
                    </div>
                </div>
            @endif

            @can('assignReviewer', $submission)
                <div class="card border-dashed mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Assign reviewer</h5>
                        <p class="text-muted fs-sm">
                            Invitation applies to <strong>version {{ $submission->version }}</strong>. Users must have the reviewer role on this journal.
                        </p>
                        <form method="POST" action="{{ platform_route('editor.submissions.assign-reviewer', $submission) }}" class="row g-2 align-items-end mt-2">
                            @csrf
                            <div class="col-md-5">
                                <label class="form-label fs-sm fw-medium">Reviewer</label>
                                <select name="reviewer_id" required class="form-select form-select-sm">
                                    <option value="">Select…</option>
                                    @foreach ($reviewerPool as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-sm fw-medium">Deadline</label>
                                <input type="date" name="deadline" required class="form-control form-control-sm" min="{{ now()->addDay()->toDateString() }}">
                            </div>
                            <div class="col-md-3">
                                <x-dash.button type="submit">Send invitation</x-dash.button>
                            </div>
                        </form>
                    </div>
                </div>
            @endcan

            @can('recordDecision', $submission)
                @if ($submission->status === \App\Enums\SubmissionStatus::UnderReview)
                    @include('dashboard.editor.partials.editorial-decision-form', [
                        'submission' => $submission,
                        'roundReviews' => $roundReviews,
                    ])
                @endif
            @endcan
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted fw-semibold fs-xxs mb-2">Author</h6>
                    <p class="fw-medium mb-1">{{ $submission->author->name }}</p>
                    <p class="text-muted fs-sm mb-2">{{ $submission->author->email }}</p>
                    <p class="text-muted fs-xs mb-0">Article type: {{ $submission->article_type }}</p>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Peer review</h5>
                    <p class="text-muted fs-sm">All invitations and assignments for this manuscript.</p>
                    <div class="table-responsive mt-2">
                        <table class="table table-sm table-bordered fs-sm mb-0">
                            <thead class="table-light">
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
                                        <td class="text-muted">v{{ $ra->round_version ?? 1 }}</td>
                                        <td>{{ $ra->reviewer->name }}</td>
                                        <td class="text-nowrap text-muted">{{ $ra->deadline->format('M j, Y') }}</td>
                                        <td>@include('partials.review-assignment-status', ['status' => $ra->status])</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-muted">No reviewers assigned yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @can('publish', $submission)
                <div class="card mb-3 border-success" style="background: #f0fdf4;">
                    <div class="card-body">
                        <h5 class="card-title">Add to issue</h5>
                        <p class="text-muted fs-sm">Slot this accepted manuscript into a <strong>draft</strong> issue. It becomes public when the issue is published.</p>
                        @if ($submission->edition_id && $submission->status === \App\Enums\SubmissionStatus::Accepted)
                            <p class="mt-2 mb-0 px-3 py-2 rounded border bg-white fs-sm">
                                Slotted in <strong>{{ $submission->edition?->label() ?? 'issue' }}</strong> (draft until published).
                            </p>
                        @elseif ($submission->status === \App\Enums\SubmissionStatus::Published && $submission->edition)
                            <p class="mt-2 mb-0 text-success fs-sm">Live in <strong>{{ $submission->edition->label() }}</strong>.</p>
                        @elseif ($editionsForPublish->isEmpty())
                            <p class="mt-2 mb-0 text-warning fs-sm">No draft issues yet. Create one under Issues &amp; volumes.</p>
                        @else
                            <form method="POST" action="{{ platform_route('editor.submissions.publish', $submission) }}" class="mt-3">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label fs-sm fw-medium">Draft issue</label>
                                    <select name="edition_id" required class="form-select form-select-sm">
                                        @foreach ($editionsForPublish as $ed)
                                            <option value="{{ $ed->id }}" @selected($submission->edition_id === $ed->id)>{{ $ed->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <x-dash.button type="submit" class="btn-success">Add to issue</x-dash.button>
                            </form>
                        @endif
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection
