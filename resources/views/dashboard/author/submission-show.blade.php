@extends('layouts.dashboard', ['activeNav' => 'author-submissions'])

@section('title', Str::limit($submission->title, 48))
@section('pageTitle', $submission->title)
@section('pageDescription', $submission->journal->name . ' · ' . str_replace('_', ' ', $submission->status->value))

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <x-dash.link :href="platform_route('author.submissions')">← My submissions</x-dash.link>
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
                    'forAuthor' => true,
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

            @can('submitRevision', $submission)
                <section class="dash-card border-amber-200 bg-amber-50/60 p-6">
                    <h2 class="text-lg font-semibold text-slate-900">Submit your revision</h2>
                    <p class="mt-1 text-sm text-slate-600">Upload a revised manuscript (required). You may optionally update title, abstract, and keywords. The version number will increase and the submission will return to the editor as <strong>submitted</strong> for the next round.</p>
                    <form method="POST" action="{{ platform_route('author.submissions.revision.store', $submission) }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                        @csrf
                        <div class="dash-field">
                            <label class="dash-field-label">Revised manuscript file</label>
                            <input type="file" name="manuscript" required accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="mt-1 block w-full text-sm text-slate-700 file:mr-4 file:rounded-md file:border-0 file:bg-teal-700 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:opacity-90">
                        </div>
                        <x-dash.input name="title" label="Title (optional — leave blank to keep current)" :value="old('title', $submission->title)" />
                        <x-dash.textarea name="abstract" label="Abstract (optional)" rows="5">{{ old('abstract', $submission->abstract) }}</x-dash.textarea>
                        <x-dash.input name="keywords" label="Keywords (optional, comma-separated)" :value="old('keywords', implode(', ', $submission->keywords ?? []))" />
                        <x-dash.button type="submit">Upload revision</x-dash.button>
                    </form>
                </section>
            @endcan
        </div>

        <div class="space-y-6">
            <section class="dash-card p-6 text-sm">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Journal</h2>
                <p class="mt-2 font-medium text-slate-900">{{ $submission->journal->name }}</p>
                <p class="mt-3 text-xs text-slate-500">Article type: {{ $submission->article_type }}</p>
                <p class="mt-3">
                    <a href="{{ journal_front_url($submission->journal, '/') }}" class="font-medium text-teal-700 hover:underline">Visit journal site</a>
                </p>
            </section>
        </div>
    </div>
@endsection
