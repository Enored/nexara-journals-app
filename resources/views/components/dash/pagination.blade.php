@props(['paginator', 'itemLabel' => 'results'])

@if ($paginator->total() > 0)
    <div {{ $attributes->merge(['class' => 'card']) }}>
        <div class="card-footer border-0">
            <x-dash.pagination-footer :paginator="$paginator" :item-label="$itemLabel" />
        </div>
    </div>
@endif
