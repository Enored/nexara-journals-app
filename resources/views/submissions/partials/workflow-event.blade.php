@php
    use App\Support\SubmissionEditorTimeline;
    $meta = $event['meta'] ?? [];
    $forAuthor = $forAuthor ?? false;
@endphp

<li class="d-flex gap-2 {{ !($isLast ?? false) ? 'mb-3 pb-3 border-bottom' : '' }}">
    <div class="flex-shrink-0 mt-1" aria-hidden="true">
        <span class="d-inline-block rounded-circle bg-primary" style="width: 8px; height: 8px;"></span>
    </div>
    <div class="flex-grow-1 min-w-0">
        <div class="rounded border bg-light px-3 py-2 fs-sm">
            <div class="d-flex flex-wrap align-items-baseline justify-content-between gap-2">
                <p class="fw-medium mb-0">{{ $event['label'] }}</p>
                <time class="text-muted fs-xs text-nowrap" datetime="{{ $event['at']->toIso8601String() }}">
                    {{ $event['at']->format('M j, Y g:i A') }}
                </time>
            </div>

            @if ($event['kind'] === 'reviewer_invited' && ! empty($meta['deadline']))
                <p class="mt-1 mb-0 text-muted">Deadline {{ $meta['deadline'] }}</p>
            @endif

            @if ($event['kind'] === 'reviewer_declined' && ! empty($meta['reason']))
                <p class="mt-1 mb-0 text-muted">{{ $meta['reason'] }}</p>
            @endif

            @if ($event['kind'] === 'review_submitted' && ! $forAuthor)
                @if (! empty($meta['recommendation']))
                    <p class="mt-1 mb-0 text-muted">
                        Recommendation: <strong>{{ $meta['recommendation'] }}</strong>
                        @if (! empty($meta['scores']))
                            · Scores O/M/C: {{ $meta['scores'] }}
                        @endif
                    </p>
                @endif
                @if (! empty($meta['comments_for_editor']))
                    <p class="mt-2 mb-1 text-uppercase text-muted fs-xs fw-semibold">Confidential comments</p>
                    <p class="mb-0" style="white-space: pre-wrap;">{{ $meta['comments_for_editor'] }}</p>
                @endif
            @endif

            @if ($event['kind'] === 'editorial_decision')
                @if (! $forAuthor && (! empty($meta['decision']) || ! empty($meta['recorded_by'])))
                    <p class="mt-1 mb-0 text-muted">
                        @if (! empty($meta['decision']))
                            <strong>{{ SubmissionEditorTimeline::formatDecisionLabel($meta['decision']) }}</strong>
                        @endif
                        @if (! empty($meta['recorded_by']))
                            @if (! empty($meta['decision'])) · @endif{{ $meta['recorded_by'] }}
                        @endif
                    </p>
                @endif
                @if (! empty($meta['decision_letter']))
                    <p class="mt-2 mb-1 text-uppercase text-muted fs-xs fw-semibold">{{ $forAuthor ? 'Letter from editor' : 'Decision letter' }}</p>
                    <p class="mb-0" style="white-space: pre-wrap;">{{ $meta['decision_letter'] }}</p>
                @endif
            @endif
        </div>
    </div>
</li>
