@props([
    'type' => 'text',
    'name' => null,
    'id' => null,
    'value' => null,
    'placeholder' => null,
    'icon' => 'search',
    'required' => false,
])

@php
    $fieldId = $id ?? $name;
@endphp

<div {{ $attributes->merge(['class' => 'app-search']) }}>
    @if ($type === 'select')
        <select
            @if ($name) name="{{ $name }}" @endif
            @if ($fieldId) id="{{ $fieldId }}" @endif
            class="form-select form-control my-1 my-md-0"
            @if ($required) required @endif
        >
            {{ $slot }}
        </select>
    @else
        <input
            type="{{ $type === 'search' ? 'search' : $type }}"
            @if ($name) name="{{ $name }}" @endif
            @if ($fieldId) id="{{ $fieldId }}" @endif
            @if ($value !== null) value="{{ $value }}" @endif
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            class="form-control my-1 my-md-0"
            @if ($required) required @endif
        />
    @endif
    <i data-lucide="{{ $icon }}" class="app-search-icon text-muted"></i>
</div>
