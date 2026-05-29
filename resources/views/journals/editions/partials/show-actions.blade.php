@if ($edition->isDraft())
    <x-dash.button
        type="button"
        variant="success"
        data-bs-toggle="modal"
        data-bs-target="#edition-publish-confirm-modal"
        title="{{ $slottedCount === 0 ? 'Add at least one accepted article before publishing' : '' }}"
    >
        <i data-lucide="rocket" class="fs-sm me-1"></i>
        Publish issue
    </x-dash.button>
@else
    <x-dash.button
        type="button"
        variant="secondary"
        data-bs-toggle="modal"
        data-bs-target="#edition-unpublish-confirm-modal"
    >
        <i data-lucide="globe" class="fs-sm me-1"></i>
        Unpublish
    </x-dash.button>
@endif

<x-dash.button
    type="button"
    variant="secondary"
    data-edition-edit-open
    data-url="{{ $editModalUrl }}"
    data-subtitle="{{ $editionSubtitle }}"
>
    <i data-lucide="pencil" class="fs-sm me-1"></i>
    Edit
</x-dash.button>

<x-dash.button
    type="button"
    variant="danger"
    data-bs-toggle="modal"
    data-bs-target="#edition-delete-modal"
>
    <i data-lucide="trash-2" class="fs-sm me-1"></i>
    Delete
</x-dash.button>
