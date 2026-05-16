@props([
    'pills' => [],
    'resetUrl' => null,
])

@if (count($pills) > 0 || $resetUrl)
    <div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2']) }}>
        <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Active filters</span>
        @foreach ($pills as $pill)
            <a href="{{ $pill['url'] }}" class="dash-filter-pill" title="Remove filter">
                <span>{{ $pill['label'] }}</span>
                <svg class="h-3.5 w-3.5 shrink-0 opacity-70" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </a>
        @endforeach
        @if ($resetUrl)
            <a href="{{ $resetUrl }}" class="dash-filter-reset">Reset all</a>
        @endif
    </div>
@endif
