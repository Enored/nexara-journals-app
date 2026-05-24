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
    const form = root.querySelector('[data-dash-auto-filter]');
    if (!form) {
        return;
    }

    let debounceTimer = null;

    const loadFromForm = (resetPage = true) => {
        const url = buildFormUrl(form, resetPage);
        loadListPartial(root, url, { push: true });
    };

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        loadFromForm(true);
    });

    form.querySelectorAll('input[type="search"]').forEach((input) => {
        input.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => loadFromForm(true), DEBOUNCE_MS);
        });
    });

    form.querySelectorAll('select').forEach((select) => {
        select.addEventListener('change', () => loadFromForm(true));
    });
}

function buildFormUrl(form, resetPage) {
    const url = new URL(form.action, window.location.origin);
    const data = new FormData(form);

    url.search = '';
    for (const [key, value] of data.entries()) {
        if (typeof value === 'string' && value !== '') {
            url.searchParams.append(key, value);
        }
    }

    if (resetPage) {
        url.searchParams.delete('page');
    }

    return url.toString();
}

async function loadListPartial(root, url, { push = true }) {
    const searchInput = root.querySelector('input[type="search"]');
    const active = document.activeElement;
    const restoreFocus = searchInput && root.contains(active) && active === searchInput
        ? { start: searchInput.selectionStart, end: searchInput.selectionEnd }
        : null;

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
            const nextSearch = root.querySelector('input[type="search"]');
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
