@props([
    'label' => null,
    'name',
    'id' => null,
])

@php($fieldId = $id ?? $name)

<div class="dash-field">
    @if ($label)
        <label for="{{ $fieldId }}" class="dash-field-label">{{ $label }}</label>
    @endif
    <select name="{{ $name }}" id="{{ $fieldId }}" {{ $attributes->merge(['class' => 'dash-select']) }}>
        {{ $slot }}
    </select>
</div>
