<form
    method="POST"
    action="{{ platform_route('admin.journals.editions.publish', [$journal, $edition]) }}"
    id="edition-publish-form"
>
    @csrf
    <p class="text-muted mb-0">
        Publishing makes slotted articles visible on the public journal site. The issue date is set automatically to today when you publish.
    </p>
    @if ($slottedCount === 0)
        <div class="alert alert-warning mb-0 mt-3 py-2" data-ajax-modal-block-submit>Add at least one accepted article before publishing.</div>
    @endif
</form>
