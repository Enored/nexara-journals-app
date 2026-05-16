const DEFAULT_DURATION = 5000;

function getHost() {
    let host = document.getElementById('dash-toast-host');
    if (!host) {
        host = document.createElement('div');
        host.id = 'dash-toast-host';
        host.className =
            'pointer-events-none fixed bottom-4 right-4 z-[60] flex w-full max-w-sm flex-col gap-2 sm:bottom-6 sm:right-6';
        host.setAttribute('aria-live', 'polite');
        host.setAttribute('aria-relevant', 'additions');
        document.body.appendChild(host);
    }

    return host;
}

export function showDashToast(message, type = 'success', duration = DEFAULT_DURATION) {
    if (!message) {
        return;
    }

    const host = getHost();
    const toast = document.createElement('div');
    toast.className = `dash-toast dash-toast-${type} dash-toast-enter`;
    toast.setAttribute('role', 'status');

    const icon =
        type === 'error'
            ? '<svg class="mt-0.5 h-5 w-5 shrink-0 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>'
            : '<svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>';

    toast.innerHTML = `
        ${icon}
        <span class="min-w-0 flex-1 pr-1">${escapeHtml(message)}</span>
        <button type="button" class="shrink-0 rounded p-0.5 text-slate-400 hover:text-slate-600" aria-label="Dismiss">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    `;

    const dismiss = () => removeToast(toast);
    toast.querySelector('button')?.addEventListener('click', dismiss);

    host.appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.remove('dash-toast-enter');
        toast.classList.add('dash-toast-visible');
    });

    const timeout = window.setTimeout(dismiss, duration);
    toast._dashToastTimeout = timeout;
}

function removeToast(toast) {
    if (toast._dashToastTimeout) {
        window.clearTimeout(toast._dashToastTimeout);
    }

    toast.classList.remove('dash-toast-visible');
    toast.classList.add('dash-toast-exit');

    window.setTimeout(() => toast.remove(), 300);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;

    return div.innerHTML;
}

export function initDashToastsFromFlash() {
    const dataEl = document.getElementById('dash-flash-data');
    if (!dataEl?.textContent) {
        return;
    }

    try {
        const data = JSON.parse(dataEl.textContent);
        if (data.status) {
            showDashToast(data.status, 'success');
        }
        if (data.error) {
            showDashToast(data.error, 'error', 7000);
        }
        if (Array.isArray(data.errors) && data.errors.length > 0) {
            for (const msg of data.errors) {
                showDashToast(msg, 'error', 7000);
            }
        }
    } catch {
        // ignore invalid JSON
    }
}

if (typeof window !== 'undefined') {
    window.showDashToast = showDashToast;
}
