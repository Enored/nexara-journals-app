@php
    $isLatest = $round['version'] === $submission->version;
@endphp

<section class="dash-card overflow-hidden">
    <div class="border-b border-slate-100 bg-slate-50/90 px-6 py-4">
        <div class="flex flex-wrap items-baseline justify-between gap-2">
            <h2 class="text-lg font-semibold text-slate-900">Version {{ $round['version'] }}</h2>
            <time class="text-sm text-slate-500" datetime="{{ $round['submitted_at']->toIso8601String() }}">
                Submitted {{ $round['submitted_at']->format('M j, Y g:i A') }}
            </time>
        </div>
        @if ($isLatest)
            <p class="mt-1 text-xs font-medium text-teal-700">Current round</p>
        @endif
    </div>

    <div class="px-6 py-5">
        <section class="pb-4">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Title</h3>
            <p class="mt-1 text-sm font-medium text-slate-900">{{ $round['title'] }}</p>
        </section>
        <section class="border-t border-slate-200 py-4">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Abstract</h3>
            <p class="mt-1 whitespace-pre-wrap text-sm leading-relaxed text-slate-800">{{ $round['abstract'] }}</p>
        </section>
        <section class="border-t border-slate-200 py-4">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Keywords</h3>
            <p class="mt-1 text-sm text-slate-800">{{ implode(', ', $round['keywords'] ?? []) ?: '—' }}</p>
        </section>
        <section class="border-t border-slate-200 pt-4">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Files</h3>
            @if ($round['files']->isEmpty())
                <p class="mt-1 text-sm text-slate-500">No files for this version.</p>
            @else
                <ul class="mt-2 divide-y divide-slate-100 text-sm">
                    @foreach ($round['files'] as $file)
                        <li class="py-2">
                            <span class="font-medium text-slate-900">{{ $file->original_name }}</span>
                            <span class="block text-xs text-slate-500">
                                {{ $file->file_type->value }}
                                · {{ number_format($file->file_size / 1024, 1) }} KB
                                · {{ $file->created_at->format('M j, Y g:i A') }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>

    @if (! empty($round['events']))
        <div class="border-t border-slate-100 px-6 py-5">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Activity</h3>
            <ol class="relative mt-4 space-y-0">
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
</section>
