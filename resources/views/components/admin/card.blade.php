@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'variant' => null,
])

@php
    $cardClass = match ($variant) {
        'success' => 'border-success border-opacity-25',
        'warning' => 'border-warning border-opacity-25',
        'danger' => 'border-danger border-opacity-25',
        'info' => 'border-info border-opacity-25',
        default => '',
    };
    $bodyClass = match ($variant) {
        'success' => 'bg-success-subtle bg-opacity-10',
        'warning' => 'bg-warning-subtle bg-opacity-10',
        'danger' => 'bg-danger-subtle bg-opacity-10',
        'info' => 'bg-info-subtle bg-opacity-10',
        default => '',
    };
@endphp

<div {{ $attributes->merge(['class' => 'card '.$cardClass]) }}>
    @if ($title)
        <div class="card-header border-light">
            <div class="d-flex align-items-center gap-2">
                @if ($icon)
                    <i data-lucide="{{ $icon }}" class="text-primary"></i>
                @endif
                <div>
                    <h5 class="card-title mb-0">{{ $title }}</h5>
                    @if ($subtitle)
                        <p class="text-muted mb-0 fs-sm">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
    <div class="card-body {{ $bodyClass }}">
        {{ $slot }}
    </div>
</div>
