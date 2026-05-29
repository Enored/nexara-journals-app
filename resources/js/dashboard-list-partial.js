const DEBOUNCE_MS = 400;
const PARTIAL_HEADER = 'X-Dash-List-Partial';

/**
 * Blade list zones: fetch HTML partial, update URL, no full page reload.
 */
export function initDashListPartials() {
    document.querySelectorAll('[data-dash-list-partial]').forEach((root) => {
        bindListPartialRoot(root);
    });

    window.addEventListener('popstate', () => {
        const root = document.querySelector('[data-dash-list-partial]');
        if (root) {
            loadListPartial(root, window.location.href, { push: false });
        }
    });
}

function bindListPartialRoot(root) {
    bindFilterForm(root);

    root.addEventListener('click', (event) => {
        const link = event.target.closest('[data-dash-partial-link]');
        if (!link || !root.contains(link)) {
            return;
        }
        event.preventDefault();
        loadListPartial(root, link.href, { push: true });
    });
}

function bindFilterForm(root) {
    const forms = root.querySelectorAll('[data-dash-auto-filter]');
    if (!forms.length) {
        return;
    }

    let debounceTimer = null;

    const loadFromForms = (resetPage = true) => {
        const url = buildCombinedFormUrl(root, resetPage);
        loadListPartial(root, url, { push: true });
    };

    forms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            loadFromForms(true);
        });

        form.querySelectorAll('input[type="search"]').forEach((input) => {
            input.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => loadFromForms(true), DEBOUNCE_MS);
            });
        });

        form.querySelectorAll('select').forEach((select) => {
            select.addEventListener('change', () => loadFromForms(true));
        });
    });
}

function buildCombinedFormUrl(root, resetPage) {
    const forms = root.querySelectorAll('[data-dash-auto-filter]');
    const firstForm = forms[0];
    const url = new URL(firstForm.action, window.location.origin);

    url.search = '';

    forms.forEach((form) => {
        const data = new FormData(form);
        for (const [key, value] of data.entries()) {
            if (typeof value === 'string' && value !== '') {
                url.searchParams.append(key, value);
            }
        }
    });

    if (resetPage) {
        url.searchParams.delete('page');
        url.searchParams.delete('vpage');
    }

    return url.toString();
}

async function loadListPartial(root, url, { push = true }) {
    const active = document.activeElement;
    let restoreFocus = null;
    if (active && active.matches('input[type="search"]') && root.contains(active)) {
        restoreFocus = {
            id: active.id || null,
            name: active.name || null,
            start: active.selectionStart,
            end: active.selectionEnd,
        };
    }

    root.classList.add('opacity-50', 'pe-none');

    try {
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                [PARTIAL_HEADER]: '1',
                Accept: 'text/html',
            },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            window.location.href = url;

            return;
        }

        root.innerHTML = await response.text();

        if (push) {
            window.history.pushState({}, '', url);
        }

        if (typeof window.lucide !== 'undefined') {
            window.lucide.createIcons();
        }

        bindFilterForm(root);

        if (restoreFocus) {
            let nextSearch = null;
            if (restoreFocus.id) {
                nextSearch = root.querySelector(`#${CSS.escape(restoreFocus.id)}`);
            } else if (restoreFocus.name) {
                nextSearch = root.querySelector(`input[name="${CSS.escape(restoreFocus.name)}"]`);
            }
            nextSearch = nextSearch || root.querySelector('input[type="search"]');
            if (nextSearch) {
                nextSearch.focus();
                try {
                    nextSearch.setSelectionRange(restoreFocus.start, restoreFocus.end);
                } catch {
                    // ignore if selection not supported
                }
            }
        }
    } catch {
        window.location.href = url;
    } finally {
        root.classList.remove('opacity-50', 'pe-none');
    }
}
