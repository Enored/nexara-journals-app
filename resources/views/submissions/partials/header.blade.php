<div class="flex flex-wrap items-start justify-between gap-4">
    <div class="min-w-0 flex-1">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $submission->journal->name }}</p>
        <h1 class="text-2xl font-bold text-slate-900">{{ $submission->title }}</h1>
        <p class="mt-2 text-sm text-slate-600">
            Submitted {{ $submission->submitted_at->format('M j, Y g:i A') }} · Version {{ $submission->version }}
        </p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        @include('partials.submission-status', ['status' => $submission->status])
    </div>
</div>
