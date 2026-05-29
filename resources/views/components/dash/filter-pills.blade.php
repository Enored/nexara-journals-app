@props([
    'pills' => [],
    'resetUrl' => null,
])

@if (count($pills) > 0 || $resetUrl)
    <div {{ $attributes->merge(['class' => 'd-flex flex-wrap align-items-center gap-2 mb-0']) }}>
        <span class="text-muted fs-xs text-uppercase fw-semibold">Active filters</span>
        @foreach ($pills as $pill)
            <a href="{{ $pill['url'] }}" class="badge bg-primary-subtle text-primary text-decoration-none d-inline-flex align-items-center gap-1" title="Remove filter" data-dash-partial-link>
                <span>{{ $pill['label'] }}</span>
                <i data-lucide="x" class="fs-xs"></i>
            </a>
        @endforeach
        @if ($resetUrl)
            <a href="{{ $resetUrl }}" class="link-secondary fs-xs fw-semibold" data-dash-partial-link>Reset all</a>
        @endif
    </div>
@endif
