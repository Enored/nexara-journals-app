@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
])

@php
    $class = match ($variant) {
        'secondary' => 'dash-btn-secondary',
        'danger' => 'dash-btn-danger',
        default => 'dash-btn-primary',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</button>
@endif
