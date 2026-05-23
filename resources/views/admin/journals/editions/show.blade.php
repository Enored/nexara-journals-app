@extends('layouts.dashboard', ['activeNav' => 'admin-journals'])

@php
    use App\Enums\EditionStatus;
    use App\Enums\SubmissionStatus;
    $slottedCount = $articles->where('status', SubmissionStatus::Accepted)->count();
    $liveCount = $articles->where('status', SubmissionStatus::Published)->count();
@endphp

@section('title', $edition->label())
@section('pageTitle', $edition->label())
@section('pageDescription', $journal->name)

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2 text-sm text-slate-600">
            <x-dash.link :href="platform_route('admin.journals.index')">Journals</x-dash.link>
            <span class="text-slate-300">/</span>
            <x-dash.link :href="platform_route('admin.journals.editions.index', $journal)">{{ $journal->name }}</x-dash.link>
            <span class="text-slate-300">/</span>
            <span class="font-medium text-slate-900">Vol. {{ $edition->volume }}, No. {{ $edition->issue }}</span>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @include('partials.edition-status', ['status' => $edition->status])
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @include('partials.dashboard.stat-card', ['label' => 'Status', 'value' => $edition->isPublished() ? 'Published' : 'Draft', 'accent' => $edition->isPublished() ? 'violet' : 'amber'])
        @include('partials.dashboard.stat-card', ['label' => 'Slotted articles', 'value' => $slottedCount, 'hint' => 'Accepted, not yet public', 'accent' => 'sky'])
        @include('partials.dashboard.stat-card', ['label' => 'Live articles', 'value' => $liveCount, 'accent' => 'violet'])
        @include('partials.dashboard.stat-card', ['label' => 'Planned date', 'value' => $edition->published_at?->format('M j, Y') ?? '—'])
    </div>

    @if ($edition->isDraft())
        <section class="dash-card mt-6 border-teal-200 bg-teal-50/40 p-6">
            <h2 class="text-lg font-semibold text-slate-900">Publish issue</h2>
            <p class="mt-1 text-sm text-slate-600">Like scheduling a release: build the issue with accepted articles below, then publish to make them visible on the public journal site.</p>
            <form method="POST" action="{{ platform_route('admin.journals.editions.publish', [$journal, $edition]) }}" class="mt-4 flex flex-wrap items-end gap-4">
                @csrf
                <div>
                    <label for="publish-date" class="dash-field-label">Release date (optional)</label>
                    <input type="date" id="publish-date" name="published_at" value="{{ old('published_at', now()->toDateString()) }}" class="dash-input mt-1">
                </div>
                <x-dash.button type="submit" class="!bg-emerald-700 hover:!bg-emerald-800" :disabled="$slottedCount === 0">Publish issue</x-dash.button>
            </form>
            @if ($slottedCount === 0)
                <p class="mt-2 text-sm text-amber-800">Add at least one accepted article before publishing.</p>
            @endif
        </section>
    @else
        <section class="dash-card mt-6 border-amber-200 bg-amber-50/50 p-6">
            <h2 class="text-lg font-semibold text-slate-900">Issue is live</h2>
            <p class="mt-1 text-sm text-slate-600">This issue is visible on the journal site. Unpublish to pull articles back to accepted (slotted) status while keeping them in this issue.</p>
            <form method="POST" action="{{ platform_route('admin.journals.editions.unpublish', [$journal, $edition]) }}" class="mt-4" onsubmit="return confirm('Unpublish this issue? Live articles will return to accepted status.');">
                @csrf
                <x-dash.button type="submit" variant="secondary">Unpublish issue</x-dash.button>
            </form>
        </section>
    @endif

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-dash.table>
                <x-slot:header>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </x-slot:header>
                <x-slot:body>
                    @forelse ($articles as $article)
                        <tr>
                            <td class="max-w-xs font-medium text-slate-900">{{ Str::limit($article->title, 56) }}</td>
                            <td class="text-slate-600">{{ $article->author->name }}</td>
                            <td>@include('partials.submission-status', ['status' => $article->status])</td>
                            <td class="whitespace-nowrap text-right text-sm">
                                @if ($edition->isDraft() && $article->status === SubmissionStatus::Accepted)
                                    <form method="POST" action="{{ platform_route('admin.journals.editions.articles.remove', [$journal, $edition, $article]) }}" class="inline" onsubmit="return confirm('Remove this article from the issue?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-slate-600 hover:underline">Remove</button>
                                    </form>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="!p-0">
                                <x-dash.empty title="No articles in this issue" description="Add accepted manuscripts below before publishing." />
                            </td>
                        </tr>
                    @endforelse
                </x-slot:body>
            </x-dash.table>
        </div>

        <div class="space-y-6">
            @if ($edition->isDraft())
                <section class="dash-card p-6">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Add article</h2>
                    <p class="mt-1 text-sm text-slate-600">Only <strong>accepted</strong> manuscripts not already in another issue.</p>
                    @if ($availableToAdd->isEmpty())
                        <p class="mt-4 text-sm text-slate-500">No accepted manuscripts available to add.</p>
                    @else
                        <form method="POST" action="{{ platform_route('admin.journals.editions.articles.assign', [$journal, $edition]) }}" class="mt-4 space-y-3">
                            @csrf
                            <div>
                                <label for="add-submission" class="dash-field-label">Manuscript</label>
                                <select id="add-submission" name="submission_id" required class="dash-select mt-1 w-full">
                                    <option value="">Select…</option>
                                    @foreach ($availableToAdd as $s)
                                        <option value="{{ $s->id }}">{{ Str::limit($s->title, 48) }} — {{ $s->author->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <x-dash.button type="submit" class="w-full">Add to issue</x-dash.button>
                        </form>
                    @endif
                </section>
            @endif

            <section class="dash-card p-6">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Issue details</h2>
                <form method="POST" action="{{ platform_route('admin.journals.editions.update', [$journal, $edition]) }}" class="mt-4 space-y-3">
                    @csrf
                    @method('PUT')
                    <div class="grid gap-3 sm:grid-cols-2">
                        <x-dash.input name="volume" type="number" label="Volume" :value="old('volume', $edition->volume)" min="1" required />
                        <x-dash.input name="issue" type="number" label="Issue" :value="old('issue', $edition->issue)" min="1" required />
                    </div>
                    <x-dash.input name="title" label="Title (optional)" :value="old('title', $edition->title)" />
                    <x-dash.input name="planned_date" type="date" label="Planned / published date" :value="old('planned_date', $edition->published_at?->format('Y-m-d'))" />
                    <x-dash.button type="submit" variant="secondary" class="w-full">Save details</x-dash.button>
                </form>
            </section>

            <section class="dash-card mt-6 border-rose-200 bg-rose-50/40 p-6">
                <h2 class="text-sm font-semibold text-rose-900">Delete issue</h2>
                <p class="mt-1 text-sm text-slate-600">
                    Permanently remove Vol. {{ $edition->volume }}, No. {{ $edition->issue }}.
                    @if ($articles->isNotEmpty())
                        All {{ $articles->count() }} article(s) will be unlinked from this issue and returned to <strong>accepted</strong> status.
                        @if ($edition->isPublished())
                            Live articles will be taken off the public journal site.
                        @endif
                    @endif
                </p>
                <form
                    method="POST"
                    action="{{ platform_route('admin.journals.editions.destroy', [$journal, $edition]) }}"
                    class="mt-4"
                    onsubmit="return confirm('Delete this issue permanently? Articles will stay accepted but no longer belong to any issue.');"
                >
                    @csrf
                    @method('DELETE')
                    <x-dash.button type="submit" variant="danger">Delete issue</x-dash.button>
                </form>
            </section>
        </div>
    </div>
@endsection
