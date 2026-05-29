@props([
    'id',
    'title',
    'size' => '',
    'submitForm' => null,
    'submitLabel' => 'Save',
])

@php
    $dialogClass = trim('modal-dialog modal-dialog-centered '.$size);
@endphp

<div
    class="modal fade"
    id="{{ $id }}"
    tabindex="-1"
    aria-labelledby="{{ $id }}-title"
    aria-hidden="true"
    @if ($submitForm) data-submit-form="{{ $submitForm }}" @endif
>
    <div class="{{ $dialogClass }}">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="{{ $id }}-title">{{ $title }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="{{ $id }}-body">
                <p class="text-muted mb-3 d-none" id="{{ $id }}-subtitle"></p>
                <div id="{{ $id }}-body-content">
                    <p class="text-muted mb-0">Loading…</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                @if ($submitForm)
                    <button type="submit" class="btn btn-primary" data-ajax-modal-submit form="{{ $submitForm }}">{{ $submitLabel }}</button>
                @endif
            </div>
        </div>
    </div>
</div>
