@props([
    'label' => null,
    'name',
    'id' => null,
])

@php($fieldId = $id ?? $name)

<div class="mb-3 {{ $attributes->get('class') }}">
    @if ($label)
        <label for="{{ $fieldId }}" class="form-label">{{ $label }}</label>
    @endif
    <select
        name="{{ $name }}"
        id="{{ $fieldId }}"
        {{ $attributes->except('class')->merge(['class' => 'form-select'.($errors->has($name) ? ' is-invalid' : '')]) }}
    >
        {{ $slot }}
    </select>
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
