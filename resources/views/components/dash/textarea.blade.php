@props([
    'label' => null,
    'name',
    'id' => null,
    'rows' => 4,
])

@php($fieldId = $id ?? $name)

<div class="mb-3 {{ $attributes->get('class') }}">
    @if ($label)
        <label for="{{ $fieldId }}" class="form-label">{{ $label }}</label>
    @endif
    <textarea
        name="{{ $name }}"
        id="{{ $fieldId }}"
        rows="{{ $rows }}"
        {{ $attributes->except('class')->merge(['class' => 'form-control'.($errors->has($name) ? ' is-invalid' : '')]) }}
    >{{ $slot->isEmpty() ? old($name) : $slot }}</textarea>
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
