@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
])

@php
    $class = match ($variant) {
        'secondary' => 'btn btn-light',
        'success' => 'btn btn-success',
        'danger' => 'btn btn-danger',
        default => 'btn btn-primary',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" @disabled($disabled) {{ $attributes->except('disabled')->merge(['class' => $class]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" @disabled($disabled) {{ $attributes->except('disabled')->merge(['class' => $class]) }}>{{ $slot }}</button>
@endif
