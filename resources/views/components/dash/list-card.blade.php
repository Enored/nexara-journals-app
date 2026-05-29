@props([
    'filterAction' => null,
    'filterMethod' => 'GET',
    'paginator' => null,
    'itemLabel' => 'results',
])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if ($filterAction || (isset($filterStart) && ! $filterStart->isEmpty()) || (isset($filterEnd) && ! $filterEnd->isEmpty()))
        @if ($filterAction)
            <form method="{{ $filterMethod }}" action="{{ $filterAction }}" data-dash-auto-filter>
        @endif
        <div class="card-header border-light d-flex flex-wrap align-items-center gap-2">
            <div class="d-flex flex-wrap align-items-center gap-2">
                {{ $filterStart ?? '' }}
            </div>
            <div class="d-flex align-items-center flex-wrap gap-1 ms-lg-auto">
                {{ $filterEnd ?? '' }}
            </div>
        </div>
        @if ($filterAction)
            </form>
        @endif
    @endif

    @if (isset($pills) && ! $pills->isEmpty())
        <div class="card-body border-bottom border-light py-2">
            {{ $pills }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-custom table-centered table-hover w-100 mb-0">
            @isset($header)
                <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                    {{ $header }}
                </thead>
            @endisset
            <tbody>{{ $body ?? $slot }}</tbody>
        </table>
    </div>

    @if ($paginator && $paginator->total() > 0)
        <div class="card-footer border-0">
            <x-dash.pagination-footer :paginator="$paginator" :item-label="$itemLabel" />
        </div>
    @endif
</div>
