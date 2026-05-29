@props(['paginator', 'itemLabel' => 'results'])

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
    <p class="text-muted mb-0">
        Showing
        <span class="fw-semibold">{{ number_format($paginator->firstItem() ?? 0) }}</span>
        to
        <span class="fw-semibold">{{ number_format($paginator->lastItem() ?? 0) }}</span>
        of
        <span class="fw-semibold">{{ number_format($paginator->total()) }}</span>
        {{ $itemLabel }}
    </p>

    @if ($paginator->hasPages())
        <nav aria-label="Pagination">
            <ul class="pagination mb-0">
                <li class="page-item @if ($paginator->onFirstPage()) disabled @endif">
                    @if ($paginator->onFirstPage())
                        <span class="page-link">Previous</span>
                    @else
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" data-dash-partial-link>Previous</a>
                    @endif
                </li>

                @php
                    $start = max(1, $paginator->currentPage() - 2);
                    $end = min($paginator->lastPage(), $paginator->currentPage() + 2);
                @endphp

                @if ($start > 1)
                    <li class="page-item"><a class="page-link" href="{{ $paginator->url(1) }}" data-dash-partial-link>1</a></li>
                    @if ($start > 2)
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    @endif
                @endif

                @for ($page = $start; $page <= $end; $page++)
                    <li class="page-item @if ($page === $paginator->currentPage()) active @endif">
                        @if ($page === $paginator->currentPage())
                            <span class="page-link">{{ $page }}</span>
                        @else
                            <a class="page-link" href="{{ $paginator->url($page) }}" data-dash-partial-link>{{ $page }}</a>
                        @endif
                    </li>
                @endfor

                @if ($end < $paginator->lastPage())
                    @if ($end < $paginator->lastPage() - 1)
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    @endif
                    <li class="page-item"><a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}" data-dash-partial-link>{{ $paginator->lastPage() }}</a></li>
                @endif

                <li class="page-item @if (! $paginator->hasMorePages()) disabled @endif">
                    @if ($paginator->hasMorePages())
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" data-dash-partial-link>Next</a>
                    @else
                        <span class="page-link">Next</span>
                    @endif
                </li>
            </ul>
        </nav>
    @endif
</div>
