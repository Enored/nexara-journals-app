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

<div class="mb-3 {{ $attributes->get('class') }}">
    @if ($label)
        <label for="{{ $fieldId }}" class="form-label">{{ $label }}</label>
    @endif
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $fieldId }}"
        @if (! in_array($type, ['file', 'checkbox', 'radio'], true)) value="{{ $resolvedValue }}" @endif
        {{ $attributes->except('class')->merge(['class' => 'form-control'.($errors->has($name) ? ' is-invalid' : '')]) }}
    />
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
