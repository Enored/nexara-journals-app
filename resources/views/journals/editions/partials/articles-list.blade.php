@php
    use App\Enums\SubmissionStatus;
    $addArticleModalUrl = platform_route('journal.editions.articles.add-form', [$journal, $edition]).'?modal=1';
    $editionSubtitle = $journal->name.' · Vol. '.$edition->volume->number.', No. '.$edition->issue;
    $hasArticleSearch = ($articleSearch ?? '') !== '';
@endphp

<x-dash.list-card
    :filter-action="platform_route('journal.editions.show', [$journal, $edition])"
    :paginator="$articles"
    item-label="articles"
>
    <x-slot:filterStart>
        <x-dash.app-search
            type="search"
            name="aq"
            id="articles-filter-q"
            :value="$articleSearch ?? ''"
            placeholder="Search title or author…"
        />
        @if ($hasArticleSearch)
            <x-dash.button variant="secondary" :href="platform_route('journal.editions.show', [$journal, $edition])" data-dash-partial-link>Reset</x-dash.button>
        @endif
    </x-slot:filterStart>
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
    @if ($hasArticleSearch)
        <x-slot:pills>
            <x-dash.filter-pills
                :pills="[['key' => 'aq', 'label' => 'Search: '.($articleSearch ?? ''), 'url' => platform_route('journal.editions.show', [$journal, $edition])]]"
                :reset-url="platform_route('journal.editions.show', [$journal, $edition])"
            />
        </x-slot:pills>
    @endif
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
                        <form method="POST" action="{{ platform_route('journal.editions.articles.remove', [$journal, $edition, $article]) }}" class="d-inline" onsubmit="return confirm('Remove this article from the issue?');">
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
                    @if ($hasArticleSearch)
                        <x-dash.empty
                            title="No articles match"
                            description="Try adjusting your search."
                        />
                    @else
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
                    @endif
                </td>
            </tr>
        @endforelse
    </x-slot:body>
</x-dash.list-card>
