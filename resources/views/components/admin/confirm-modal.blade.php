@props([
    'id',
    'title',
    'confirmLabel' => 'Confirm',
    'confirmVariant' => 'danger',
    'formId' => null,
    'size' => '',
])

@php
    $dialogClass = trim('modal-dialog modal-dialog-centered '.$size);
    $confirmClass = match ($confirmVariant) {
        'primary' => 'btn btn-primary',
        'secondary' => 'btn btn-secondary',
        'success' => 'btn btn-success',
        default => 'btn btn-danger',
    };
@endphp

<div class="modal fade" id="{{ $id }}" data-admin-confirm-modal tabindex="-1" aria-labelledby="{{ $id }}-title" aria-hidden="true">
    <div class="{{ $dialogClass }}">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="{{ $id }}-title">{{ $title }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                @if ($formId)
                    <button type="submit" class="{{ $confirmClass }}" form="{{ $formId }}">{{ $confirmLabel }}</button>
                @else
                    <button type="button" class="{{ $confirmClass }}" data-confirm-modal-confirm>{{ $confirmLabel }}</button>
                @endif
            </div>
        </div>
    </div>
</div>
