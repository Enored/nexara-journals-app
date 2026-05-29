@props(['label', 'value', 'hint' => null, 'accent' => 'primary'])

@php
    $iconBg = match ($accent) {
        'sky' => 'bg-info-subtle text-info',
        'amber' => 'bg-warning-subtle text-warning',
        'violet' => 'bg-purple-subtle text-purple',
        'rose' => 'bg-danger-subtle text-danger',
        default => 'bg-primary-subtle text-primary',
    };
@endphp

<div class="col">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="avatar fs-60 avatar-img-size flex-shrink-0">
                    <span class="avatar-title {{ $iconBg }} rounded-circle fs-24">
                        <i data-lucide="bar-chart-3"></i>
                    </span>
                </div>
                <div class="text-end">
                    <h3 class="mb-1 fw-normal">{{ $value }}</h3>
                    <p class="mb-0 text-muted"><span>{{ $label }}</span></p>
                    @if ($hint)
                        <p class="mb-0 mt-1 text-muted fs-xs">{{ $hint }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
