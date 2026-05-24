@extends('layouts.app')

@section('title', $journal->name)

@section('content')
    <div @if($journal->primary_color) style="--journal-primary: {{ $journal->primary_color }}" @endif class="space-y-10">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">{{ $journal->name }}</h1>
                @if ($journal->issn)
                    <p class="mt-1 text-sm text-slate-500">ISSN {{ $journal->issn }}</p>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @auth
                    <a href="{{ route('journal.submit.create') }}" class="rounded-md bg-journal-primary px-4 py-2 text-sm font-semibold text-white hover:opacity-90">Submit manuscript</a>
                    @if (auth()->user()->isPlatformAdmin())
                        <a href="{{ platform_route('admin.journals.editions.index', $journal) }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Issues &amp; volumes</a>
                    @endif
                @else
                    <a href="{{ platform_route('login') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Log in to submit</a>
                @endauth
            </div>
        </div>

        @if ($journal->description)
            <div class="prose prose-slate max-w-none text-slate-700">
                <p>{{ $journal->description }}</p>
            </div>
        @endif

        @if ($forthcoming->isNotEmpty())
            <section>
                <h2 class="text-lg font-semibold text-slate-900">Forthcoming (accepted)</h2>
                <p class="mt-1 text-sm text-slate-600">Accepted manuscripts not yet assigned to an issue for publication.</p>
                <ul class="mt-4 divide-y divide-slate-200 rounded-lg border border-slate-200 bg-white">
                    @foreach ($forthcoming as $m)
                        <li class="flex flex-wrap items-baseline justify-between gap-2 px-4 py-3 text-sm">
                            <div>
                                @auth
                                    @can('view', $m)
                                        <a href="{{ submission_workspace_route($m) ?? '#' }}" class="font-medium text-journal-primary hover:underline">{{ $m->title }}</a>
                                    @else
                                        <span class="font-medium text-slate-900">{{ $m->title }}</span>
                                    @endcan
                                @else
                                    <span class="font-medium text-slate-900">{{ $m->title }}</span>
                                @endauth
                                <p class="mt-0.5 text-xs text-slate-500">{{ $m->author->name }}</p>
                            </div>
                            @include('partials.submission-status', ['status' => $m->status])
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        <section>
            <h2 class="text-lg font-semibold text-slate-900">Published articles</h2>
            @if ($publishedByEdition->isEmpty())
                <p class="mt-2 text-sm text-slate-600">No published articles yet. When editors assign accepted work to an issue and publish it, listings appear here.</p>
            @else
                <div class="mt-4 space-y-8">
                    @foreach ($publishedByEdition as $editionId => $manuscripts)
                        @php($edition = $manuscripts->first()->edition)
                        <div class="rounded-lg border border-slate-200 bg-white p-5">
                            <h3 class="text-base font-semibold text-slate-900">
                                Vol. {{ $edition->volume->number }}, No. {{ $edition->issue }}
                                @if ($edition->title)
                                    <span class="font-normal text-slate-600">— {{ $edition->title }}</span>
                                @endif
                            </h3>
                            @if ($edition->published_at)
                                <p class="mt-1 text-xs text-slate-500">Issue date {{ $edition->published_at->format('F j, Y') }}</p>
                            @endif
                            <ul class="mt-4 divide-y divide-slate-100">
                                @foreach ($manuscripts as $m)
                                    <li class="py-3">
                                        <a href="{{ route('journal.articles.show', $m) }}" class="font-medium text-journal-primary hover:underline">{{ $m->title }}</a>
                                        <p class="mt-1 text-xs text-slate-600">{{ $m->author->name }} · {{ str_replace('_', ' ', $m->article_type) }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        @if ($journal->submission_guidelines)
            <div class="rounded-lg border border-slate-200 bg-white p-6">
                <h2 class="text-lg font-semibold text-slate-900">Submission guidelines</h2>
                <div class="mt-3 whitespace-pre-wrap text-sm text-slate-600">{{ $journal->submission_guidelines }}</div>
            </div>
        @endif
    </div>
@endsection
