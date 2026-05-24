/**
 * Confirm modals: static (Blade form-id) and dynamic (data-admin-confirm-open triggers).
 */
const CONFIRM_VARIANT_CLASSES = {
    danger: 'btn btn-danger',
    primary: 'btn btn-primary',
    success: 'btn btn-success',
    secondary: 'btn btn-secondary',
    warning: 'btn btn-warning',
};

export function initConfirmModals() {
    if (!window.bootstrap?.Modal) {
        return;
    }

    document.querySelectorAll('[data-admin-confirm-modal]').forEach((modalEl) => {
        if (modalEl.parentElement !== document.body) {
            document.body.appendChild(modalEl);
        }
    });

    if (document.documentElement.dataset.adminConfirmOpenBound === '1') {
        return;
    }

    document.documentElement.dataset.adminConfirmOpenBound = '1';

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-admin-confirm-open]');

        if (!trigger) {
            return;
        }

        event.preventDefault();

        const modalId = trigger.dataset.confirmModal || 'admin-action-confirm-modal';
        const modalEl = document.getElementById(modalId);

        if (!modalEl) {
            return;
        }

        openConfirmModal(modalEl, trigger);
    });
}

function openConfirmModal(modalEl, trigger) {
    const titleEl = modalEl.querySelector('[data-confirm-modal-title]');
    const bodyEl = modalEl.querySelector('[data-confirm-modal-body]');
    const confirmBtn = modalEl.querySelector('[data-confirm-modal-confirm]');

    if (titleEl) {
        titleEl.textContent = trigger.dataset.confirmTitle || 'Confirm action';
    }

    if (bodyEl) {
        bodyEl.textContent = trigger.dataset.confirmMessage || '';
    }

    if (confirmBtn) {
        const variant = trigger.dataset.confirmVariant || 'danger';

        confirmBtn.textContent = trigger.dataset.confirmLabel || 'Confirm';
        confirmBtn.className = CONFIRM_VARIANT_CLASSES[variant] || CONFIRM_VARIANT_CLASSES.danger;

        const formId = trigger.dataset.confirmForm;

        if (formId) {
            confirmBtn.setAttribute('form', formId);
            confirmBtn.type = 'submit';
        } else {
            confirmBtn.removeAttribute('form');
            confirmBtn.type = 'button';
        }
    }

    window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
}
