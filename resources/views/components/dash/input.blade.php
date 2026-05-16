@props([
    'label' => null,
    'name',
    'type' => 'text',
    'value' => null,
    'id' => null,
])

@php
    $fieldId = $id ?? $name;
    $resolvedValue = $value ?? old($name);
@endphp

<div class="dash-field">
    @if ($label)
        <label for="{{ $fieldId }}" class="dash-field-label">{{ $label }}</label>
    @endif
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $fieldId }}"
        @if (! in_array($type, ['file', 'checkbox', 'radio'], true)) value="{{ $resolvedValue }}" @endif
        {{ $attributes->merge(['class' => 'dash-input']) }}
    />
</div>
