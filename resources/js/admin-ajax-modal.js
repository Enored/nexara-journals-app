/**
 * Load HTML into a Bootstrap modal via fetch (admin forms).
 * Uses document-level delegation so buttons inside AJAX list partials work.
 */
export function initAjaxModal(config) {
    const modalEl = document.getElementById(config.id);
    if (!modalEl || !window.bootstrap?.Modal) {
        return;
    }

    const bodyContentEl = modalEl.querySelector(`#${config.id}-body-content`);
    const subtitleEl = modalEl.querySelector(`#${config.id}-subtitle`);

    if (!bodyContentEl) {
        return;
    }

    if (modalEl.parentElement !== document.body) {
        document.body.appendChild(modalEl);
    }

    const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
    const submitFormId = config.submitForm || modalEl.dataset.submitForm || null;
    const submitBtn = modalEl.querySelector('[data-ajax-modal-submit]');
    let lastTrigger = null;

    const setSubtitle = (text) => {
        if (!subtitleEl) {
            return;
        }
        if (text) {
            subtitleEl.textContent = text;
            subtitleEl.classList.remove('d-none');
        } else {
            subtitleEl.textContent = '';
            subtitleEl.classList.add('d-none');
        }
    };

    const setLoading = () => {
        setSubtitle('');
        bodyContentEl.innerHTML = '<p class="text-muted mb-0">Loading…</p>';
    };

    const setError = (message) => {
        setSubtitle('');
        bodyContentEl.innerHTML = `<div class="alert alert-danger mb-0">${message}</div>`;
    };

    const syncSubmitButton = () => {
        if (!submitBtn) {
            return;
        }
        const formId = submitFormId || submitBtn.getAttribute('form');
        const form = formId
            ? bodyContentEl.querySelector(`#${CSS.escape(formId)}`)
            : null;
        const blocked = bodyContentEl.querySelector('[data-ajax-modal-block-submit]');
        const canSubmit = form && !blocked;
        submitBtn.disabled = !canSubmit;
        submitBtn.classList.toggle('d-none', !canSubmit);
    };

    const load = async (url, subtitle = '') => {
        if (!url) {
            return;
        }

        setSubtitle(subtitle);
        bodyContentEl.innerHTML = '<p class="text-muted mb-0">Loading…</p>';
        modal.show();

        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'text/html' },
                credentials: 'same-origin',
            });
            if (!response.ok) {
                throw new Error('Failed to load');
            }
            bodyContentEl.innerHTML = await response.text();
            if (typeof window.lucide !== 'undefined') {
                window.lucide.createIcons();
            }
            syncSubmitButton();
        } catch {
            setError('Could not load content. Please try again.');
        }
    };

    document.addEventListener('click', (event) => {
        const btn = event.target.closest(`[${config.openAttribute}]`);
        if (!btn || btn.disabled || btn.getAttribute('aria-disabled') === 'true') {
            return;
        }
        event.preventDefault();
        lastTrigger = btn;
        const subtitleParts = btn.dataset.subtitle
            || [btn.dataset.userName, btn.dataset.userEmail, btn.dataset.journalName]
                .filter(Boolean)
                .join(' · ');
        load(btn.dataset.url, subtitleParts);
    });

    modalEl.addEventListener('hidden.bs.modal', () => {
        setLoading();
        lastTrigger?.focus();
        lastTrigger = null;
    });
}
