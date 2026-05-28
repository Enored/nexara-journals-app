@extends('layouts.dashboard', ['activeNav' => $editionActiveNav ?? 'admin-journals'])

@php
    use App\Enums\SubmissionStatus;
    $slottedCount = $edition->submissions()->where('status', SubmissionStatus::Accepted)->count();
    $liveCount = $edition->submissions()->where('status', SubmissionStatus::Published)->count();
    $totalArticles = $slottedCount + $liveCount;
    $editionSubtitle = $journal->name.' · Vol. '.$edition->volume->number.', No. '.$edition->issue;
    $editModalUrl = platform_route('journal.editions.edit', [$journal, $edition]).'?modal=1';
    $addArticleModalUrl = platform_route('journal.editions.articles.add-form', [$journal, $edition]).'?modal=1';
    $publishModalUrl = platform_route('journal.editions.publish-form', [$journal, $edition]).'?modal=1';
@endphp

@section('title', $edition->label())
@section('pageTitle', $edition->label())
@section('pageDescription', $journal->name)

@section('breadcrumb')
    <x-admin.breadcrumb :items="[
        ['label' => $editionsParentLabel, 'url' => $editionsParentUrl],
        ['label' => $journal->name, 'url' => platform_route('journal.editions.index', $journal)],
        ['label' => 'Vol. '.$edition->volume->number.', No. '.$edition->issue, 'aria' => true],
    ]" />
@endsection

@section('content')
    <div class="card mb-4">
        <div class="card-header border-light d-flex flex-wrap align-items-center gap-2">
            <h5 class="card-title mb-0">Issue details</h5>
            <div class="d-flex flex-wrap align-items-center gap-2 ms-lg-auto">
                @include('journals.editions.partials.show-actions', compact(
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

    <x-dash.list-partial-zone>
        @include('journals.editions.partials.articles-list')
    </x-dash.list-partial-zone>

    {{-- Hidden forms --}}
    <form method="POST" action="{{ platform_route('journal.editions.destroy', [$journal, $edition]) }}" id="edition-delete-form" class="d-none">
        @csrf
        @method('DELETE')
    </form>
    <form method="POST" action="{{ platform_route('journal.editions.publish', [$journal, $edition]) }}" id="edition-publish-form" class="d-none">
        @csrf
    </form>
    <form method="POST" action="{{ platform_route('journal.editions.unpublish', [$journal, $edition]) }}" id="edition-unpublish-form" class="d-none">
        @csrf
    </form>
@endsection

@push('modals')
    {{-- Publish confirmation --}}
    <x-admin.confirm-modal
        id="edition-publish-confirm-modal"
        title="Publish this issue?"
        confirm-label="Publish issue"
        form-id="edition-publish-form"
        confirm-variant="success"
    >
        <p class="mb-2">This will publish <strong>Vol. {{ $edition->volume->number }}, No. {{ $edition->issue }}</strong> on the {{ $journal->name }} journal site.</p>
        @if ($slottedCount > 0)
            <p class="mb-0 text-muted">
                {{ $slottedCount }} slotted article(s) will become publicly visible immediately.
            </p>
        @else
            <p class="mb-0 text-warning">
                <strong>Warning:</strong> No articles are slotted yet. The issue will be published empty.
            </p>
        @endif
    </x-admin.confirm-modal>

    {{-- Unpublish confirmation --}}
    <x-admin.confirm-modal
        id="edition-unpublish-confirm-modal"
        title="Unpublish this issue?"
        confirm-label="Unpublish issue"
        form-id="edition-unpublish-form"
    >
        <p class="mb-2">This will take <strong>Vol. {{ $edition->volume->number }}, No. {{ $edition->issue }}</strong> offline from the {{ $journal->name }} journal site.</p>
        @if ($liveCount > 0)
            <p class="mb-0 text-muted">
                {{ $liveCount }} live article(s) will return to <strong>accepted</strong> status but remain slotted in this issue.
            </p>
        @else
            <p class="mb-0 text-muted">The issue has no live articles. It will simply return to draft status.</p>
        @endif
    </x-admin.confirm-modal>

    {{-- Delete confirmation --}}
    <x-admin.confirm-modal
        id="edition-delete-modal"
        title="Delete this issue?"
        confirm-label="Delete issue"
        form-id="edition-delete-form"
    >
        <p class="mb-2">This permanently removes <strong>Vol. {{ $edition->volume->number }}, No. {{ $edition->issue }}</strong> from {{ $journal->name }}.</p>
        @if ($totalArticles > 0)
            <p class="mb-0 text-muted">
                All {{ $totalArticles }} article(s) will be unlinked and returned to <strong>accepted</strong> status.
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
@endpush
