@extends('layouts.dashboard', ['activeNav' => 'admin-journals'])

@php
    use App\Enums\SubmissionStatus;
    $slottedCount = $articles->where('status', SubmissionStatus::Accepted)->count();
    $liveCount = $articles->where('status', SubmissionStatus::Published)->count();
    $editionSubtitle = $journal->name.' · Vol. '.$edition->volume->number.', No. '.$edition->issue;
    $editModalUrl = platform_route('admin.journals.editions.edit', [$journal, $edition]).'?modal=1';
    $addArticleModalUrl = platform_route('admin.journals.editions.articles.add-form', [$journal, $edition]).'?modal=1';
    $publishModalUrl = platform_route('admin.journals.editions.publish-form', [$journal, $edition]).'?modal=1';
@endphp

@section('title', $edition->label())
@section('pageTitle', $edition->label())
@section('pageDescription', $journal->name)

@section('breadcrumb')
    <x-admin.breadcrumb :items="[
        ['label' => 'Journals', 'url' => platform_route('admin.journals.index')],
        ['label' => $journal->name, 'url' => platform_route('admin.journals.editions.index', $journal)],
        ['label' => 'Vol. '.$edition->volume->number.', No. '.$edition->issue, 'aria' => true],
    ]" />
@endsection

@section('content')
    <div class="card mb-4">
        <div class="card-header border-light d-flex flex-wrap align-items-center gap-2">
            <h5 class="card-title mb-0">Issue details</h5>
            <div class="d-flex flex-wrap align-items-center gap-2 ms-lg-auto">
                @include('admin.journals.editions.partials.show-actions', compact(
                    'journal',
                    'edition',
                    'editionSubtitle',
                    'editModalUrl',
                    'publishModalUrl',
                    'slottedCount',
                ))
            </div>
        </div>
        <div class="card-body">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
                <div>
                    <p class="text-muted text-uppercase fs-xxs mb-1">Volume / issue</p>
                    <p class="fw-semibold mb-0">Vol. {{ $edition->volume->number }}, No. {{ $edition->issue }}</p>
                </div>
                <div>
                    <p class="text-muted text-uppercase fs-xxs mb-1">Title</p>
                    <p class="fw-semibold mb-0">{{ $edition->title ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-muted text-uppercase fs-xxs mb-1">Status</p>
                    <div>@include('partials.edition-status', ['status' => $edition->status])</div>
                </div>
                <div>
                    <p class="text-muted text-uppercase fs-xxs mb-1">Published date</p>
                    <p class="fw-semibold mb-0">{{ $edition->isPublished() ? $edition->published_at?->format('M j, Y') : '—' }}</p>
                </div>
                <div>
                    <p class="text-muted text-uppercase fs-xxs mb-1">Slotted articles</p>
                    <p class="fw-semibold mb-0">{{ $slottedCount }}</p>
                    <p class="text-muted fs-xs mb-0">Accepted, not yet public</p>
                </div>
                <div>
                    <p class="text-muted text-uppercase fs-xxs mb-1">Live articles</p>
                    <p class="fw-semibold mb-0">{{ $liveCount }}</p>
                </div>
            </div>

            @if ($edition->isDraft())
                <p class="text-muted mb-0 mt-3 pt-3 border-top border-light">
                    Slot accepted manuscripts below, then publish to make them visible on the journal site.
                    @if ($slottedCount === 0)
                        <span class="d-block mt-1">Add at least one article before publishing.</span>
                    @endif
                </p>
            @else
                <p class="text-muted mb-0 mt-3 pt-3 border-top border-light">
                    This issue is live on the journal site. You can add more accepted articles — they go public immediately. Use <strong>Unpublish</strong> to pull all articles back to accepted (slotted) status.
                </p>
            @endif
        </div>
    </div>

    <x-dash.list-card>
        <x-slot:filterEnd>
            <x-dash.button
                type="button"
                data-edition-add-article-open
                data-url="{{ $addArticleModalUrl }}"
                data-subtitle="{{ $editionSubtitle }}"
            >
                <i data-lucide="plus" class="fs-sm me-1"></i>
                Add article
            </x-dash.button>
        </x-slot:filterEnd>
        <x-slot:header>
            <tr class="text-uppercase fs-xxs">
                <th>Title</th>
                <th>Author</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
        </x-slot:header>
        <x-slot:body>
            @forelse ($articles as $article)
                <tr>
                    <td class="fw-medium" style="max-width: 18rem;">{{ Str::limit($article->title, 56) }}</td>
                    <td class="text-muted">{{ $article->author->name }}</td>
                    <td>@include('partials.submission-status', ['status' => $article->status])</td>
                    <td class="text-end text-nowrap">
                        @if ($edition->isDraft() && $article->status === SubmissionStatus::Accepted)
                            <form method="POST" action="{{ platform_route('admin.journals.editions.articles.remove', [$journal, $edition, $article]) }}" class="d-inline" onsubmit="return confirm('Remove this article from the issue?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link btn-sm link-secondary p-0">Remove</button>
                            </form>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-0">
                        <x-dash.empty
                            title="No articles in this issue"
                            :description="$edition->isDraft()
                                ? 'Use Add article to slot accepted manuscripts before publishing.'
                                : 'Use Add article to publish accepted manuscripts into this live issue.'"
                        >
                            <x-dash.button
                                type="button"
                                data-edition-add-article-open
                                data-url="{{ $addArticleModalUrl }}"
                                data-subtitle="{{ $editionSubtitle }}"
                            >
                                Add article
                            </x-dash.button>
                        </x-dash.empty>
                    </td>
                </tr>
            @endforelse
        </x-slot:body>
    </x-dash.list-card>

    <form
        method="POST"
        action="{{ platform_route('admin.journals.editions.destroy', [$journal, $edition]) }}"
        id="edition-delete-form"
        class="d-none"
    >
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('modals')
    <x-admin.confirm-modal
        id="edition-delete-modal"
        title="Delete this issue?"
        confirm-label="Delete issue"
        form-id="edition-delete-form"
    >
        <p class="mb-2">This permanently removes <strong>Vol. {{ $edition->volume->number }}, No. {{ $edition->issue }}</strong> from {{ $journal->name }}.</p>
        @if ($articles->isNotEmpty())
            <p class="mb-0 text-muted">
                All {{ $articles->count() }} article(s) will be unlinked and returned to <strong>accepted</strong> status.
                @if ($edition->isPublished())
                    Live articles will be removed from the public journal site.
                @endif
            </p>
        @else
            <p class="mb-0 text-muted">This issue has no articles. The issue record will be removed only.</p>
        @endif
    </x-admin.confirm-modal>

    <x-admin.ajax-modal
        id="edition-edit-modal"
        title="Issue details"
        submit-form="edition-edit-form"
        submit-label="Save details"
    />
    <x-admin.ajax-modal
        id="edition-add-article-modal"
        title="Add article"
        submit-form="edition-add-article-form"
        submit-label="Add to issue"
    />
    <x-admin.ajax-modal
        id="edition-publish-modal"
        title="Publish issue"
        submit-form="edition-publish-form"
        submit-label="Publish issue"
    />
@endpush
