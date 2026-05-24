@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'd-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 '.$class]) }}>
    <div class="flex-grow-1 min-w-0">
        {{ $breadcrumb ?? '' }}
    </div>
    @if (isset($actions) && ! $actions->isEmpty())
        <div class="d-flex flex-wrap gap-2">
            {{ $actions }}
        </div>
    @endif
</div>
