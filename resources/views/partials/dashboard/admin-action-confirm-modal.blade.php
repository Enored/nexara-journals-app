{{--
    Shared confirmation dialog for destructive or sensitive dashboard actions.
    Trigger with data-admin-confirm-open on a button (see admin-confirm-modal.js).
--}}
<div
    class="modal fade"
    id="admin-action-confirm-modal"
    data-admin-confirm-modal
    tabindex="-1"
    aria-labelledby="admin-action-confirm-modal-title"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="admin-action-confirm-modal-title" data-confirm-modal-title>Confirm action</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0" data-confirm-modal-body></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger" data-confirm-modal-confirm>Confirm</button>
            </div>
        </div>
    </div>
</div>
