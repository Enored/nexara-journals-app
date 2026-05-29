@php
    $isLatest = $round['version'] === $submission->version;
@endphp

<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="d-flex flex-wrap align-items-baseline justify-content-between gap-2">
            <h5 class="mb-0">Version {{ $round['version'] }}</h5>
            <time class="text-muted fs-sm" datetime="{{ $round['submitted_at']->toIso8601String() }}">
                Submitted {{ $round['submitted_at']->format('M j, Y g:i A') }}
            </time>
        </div>
        @if ($isLatest)
            <p class="mb-0 mt-1 fs-xs fw-medium text-success">Current round</p>
        @endif
    </div>

    <div class="card-body">
        <div class="mb-3">
            <h6 class="text-uppercase text-muted fs-xxs fw-semibold">Title</h6>
            <p class="mb-0 fw-medium">{{ $round['title'] }}</p>
        </div>
        <div class="mb-3 pt-2 border-top">
            <h6 class="text-uppercase text-muted fs-xxs fw-semibold">Abstract</h6>
            <p class="mb-0 fs-sm" style="white-space: pre-wrap;">{{ $round['abstract'] }}</p>
        </div>
        <div class="mb-3 pt-2 border-top">
            <h6 class="text-uppercase text-muted fs-xxs fw-semibold">Keywords</h6>
            <p class="mb-0 fs-sm">{{ implode(', ', $round['keywords'] ?? []) ?: '—' }}</p>
        </div>
        <div class="pt-2 border-top">
            <h6 class="text-uppercase text-muted fs-xxs fw-semibold">Files</h6>
            @if ($round['files']->isEmpty())
                <p class="mb-0 text-muted fs-sm">No files for this version.</p>
            @else
                <ul class="list-unstyled mb-0">
                    @foreach ($round['files'] as $file)
                        <li class="py-1 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <span class="fw-medium">{{ $file->original_name }}</span>
                            <span class="d-block text-muted fs-xs">
                                {{ $file->file_type->value }}
                                · {{ number_format($file->file_size / 1024, 1) }} KB
                                · {{ $file->created_at->format('M j, Y g:i A') }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    @if (! empty($round['events']))
        <div class="card-footer bg-white border-top">
            <h6 class="text-uppercase text-muted fs-xxs fw-semibold mb-3">Activity</h6>
            <ol class="list-unstyled mb-0">
                @foreach ($round['events'] as $event)
                    @include('submissions.partials.workflow-event', [
                        'event' => $event,
                        'isLast' => $loop->last,
                        'forAuthor' => $forAuthor ?? false,
                    ])
                @endforeach
            </ol>
        </div>
    @endif
</div>
