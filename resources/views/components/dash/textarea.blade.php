@props([
    'label' => null,
    'name',
    'id' => null,
    'rows' => 4,
])

@php($fieldId = $id ?? $name)

<div class="dash-field">
    @if ($label)
        <label for="{{ $fieldId }}" class="dash-field-label">{{ $label }}</label>
    @endif
    <textarea name="{{ $name }}" id="{{ $fieldId }}" rows="{{ $rows }}" {{ $attributes->merge(['class' => 'dash-textarea']) }}>{{ $slot->isEmpty() ? old($name) : $slot }}</textarea>
</div>
