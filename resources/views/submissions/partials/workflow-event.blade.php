@php
    use App\Support\SubmissionEditorTimeline;
    $meta = $event['meta'] ?? [];
    $forAuthor = $forAuthor ?? false;
@endphp

<li class="relative flex items-stretch gap-3">
    <div class="relative w-5 shrink-0" aria-hidden="true">
        <span class="absolute left-1/2 top-1.5 z-10 h-2.5 w-2.5 -translate-x-1/2 rounded-full bg-teal-600 ring-4 ring-white"></span>
        @if (! ($isLast ?? false))
            <span class="absolute left-1/2 top-[1.375rem] bottom-0 w-px -translate-x-1/2 bg-slate-200"></span>
        @endif
    </div>
    <div class="flex min-w-0 flex-1 flex-col">
        <div class="rounded-lg border border-slate-100 bg-slate-50/80 px-4 py-3 text-sm">
            <div class="flex flex-wrap items-baseline justify-between gap-2">
                <p class="font-medium text-slate-900">{{ $event['label'] }}</p>
                <time class="shrink-0 text-xs text-slate-500" datetime="{{ $event['at']->toIso8601String() }}">
                    {{ $event['at']->format('M j, Y g:i A') }}
                </time>
            </div>

            @if ($event['kind'] === 'reviewer_invited' && ! empty($meta['deadline']))
                <p class="mt-1 text-slate-600">Deadline {{ $meta['deadline'] }}</p>
            @endif

            @if ($event['kind'] === 'reviewer_declined' && ! empty($meta['reason']))
                <p class="mt-1 text-slate-600">{{ $meta['reason'] }}</p>
            @endif

            @if ($event['kind'] === 'review_submitted')
                @if (! empty($meta['recommendation']))
                    <p class="mt-1 text-slate-600">
                        Recommendation: <strong>{{ $meta['recommendation'] }}</strong>
                        @if (! $forAuthor && ! empty($meta['scores']))
                            · Scores O/M/C: {{ $meta['scores'] }}
                        @endif
                    </p>
                @endif
                @if (! $forAuthor && ! empty($meta['comments_for_editor']))
                    <p class="mt-2 text-xs font-semibold uppercase text-slate-500">Comments for editor</p>
                    <p class="mt-1 whitespace-pre-wrap text-slate-800">{{ $meta['comments_for_editor'] }}</p>
                @endif
                @if (! empty($meta['comments_for_author']))
                    <p class="mt-2 text-xs font-semibold uppercase text-slate-500">{{ $forAuthor ? 'Comments' : 'Comments for author' }}</p>
                    <p class="mt-1 whitespace-pre-wrap text-slate-800">{{ $meta['comments_for_author'] }}</p>
                @endif
            @endif

            @if ($event['kind'] === 'editorial_decision')
                @if (! empty($meta['decision']) || ! empty($meta['recorded_by']))
                    <p class="mt-1 text-slate-600">
                        @if (! empty($meta['decision']))
                            <strong>{{ SubmissionEditorTimeline::formatDecisionLabel($meta['decision']) }}</strong>
                        @endif
                        @if (! empty($meta['recorded_by']))
                            @if (! empty($meta['decision'])) · @endif{{ $meta['recorded_by'] }}
                        @endif
                    </p>
                @endif
                @if (! empty($meta['decision_letter']))
                    <p class="mt-2 text-xs font-semibold uppercase text-slate-500">Decision letter</p>
                    <p class="mt-1 whitespace-pre-wrap text-slate-800">{{ $meta['decision_letter'] }}</p>
                @endif
            @endif
        </div>
        @if (! ($isLast ?? false))
            <div class="h-6 shrink-0" aria-hidden="true"></div>
        @endif
    </div>
</li>
