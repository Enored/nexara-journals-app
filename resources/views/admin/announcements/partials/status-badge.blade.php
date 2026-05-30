@props(['status'])

@php
    $classes = match ($status->value) {
        'published' => 'badge-soft-success',
        'archived' => 'badge-soft-secondary',
        default => 'badge-soft-warning',
    };
@endphp

<span class="badge {{ $classes }}">{{ $status->label() }}</span>
