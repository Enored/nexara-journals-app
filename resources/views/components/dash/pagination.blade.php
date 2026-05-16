@props(['paginator', 'itemLabel' => 'results'])

@if ($paginator->total() > 0)
    <div {{ $attributes->merge(['class' => 'mt-4 border-t border-slate-100 pt-4']) }}>
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <p class="text-sm text-slate-600">
                Showing
                <span class="font-medium text-slate-900">{{ number_format($paginator->firstItem() ?? 0) }}</span>–<span class="font-medium text-slate-900">{{ number_format($paginator->lastItem() ?? 0) }}</span>
                of <span class="font-medium text-slate-900">{{ number_format($paginator->total()) }}</span>
                {{ $itemLabel }}
            </p>

            @if ($paginator->hasPages())
                <nav class="flex flex-wrap items-center gap-1" aria-label="Pagination">
                    @if ($paginator->onFirstPage())
                        <span class="dash-pagination-btn dash-pagination-btn-disabled" aria-disabled="true">Previous</span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" class="dash-pagination-btn" rel="prev">Previous</a>
                    @endif

                    @php
                        $start = max(1, $paginator->currentPage() - 2);
                        $end = min($paginator->lastPage(), $paginator->currentPage() + 2);
                    @endphp

                    @if ($start > 1)
                        <a href="{{ $paginator->url(1) }}" class="dash-pagination-num">1</a>
                        @if ($start > 2)
                            <span class="dash-pagination-ellipsis" aria-hidden="true">…</span>
                        @endif
                    @endif

                    @for ($page = $start; $page <= $end; $page++)
                        @if ($page === $paginator->currentPage())
                            <span class="dash-pagination-num dash-pagination-num-active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $paginator->url($page) }}" class="dash-pagination-num">{{ $page }}</a>
                        @endif
                    @endfor

                    @if ($end < $paginator->lastPage())
                        @if ($end < $paginator->lastPage() - 1)
                            <span class="dash-pagination-ellipsis" aria-hidden="true">…</span>
                        @endif
                        <a href="{{ $paginator->url($paginator->lastPage()) }}" class="dash-pagination-num">{{ $paginator->lastPage() }}</a>
                    @endif

                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" class="dash-pagination-btn" rel="next">Next</a>
                    @else
                        <span class="dash-pagination-btn dash-pagination-btn-disabled" aria-disabled="true">Next</span>
                    @endif
                </nav>
            @endif
        </div>
    </div>
@endif
