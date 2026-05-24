/**
 * Fixed UBold layout for production admin. Only light/dark scheme is user-toggleable.
 */
(function () {
    const html = document.documentElement;
    const SCHEME_KEY = 'dash-color-scheme';

    const fixedLayout = {
        skin: 'modern',
        'topbar-color': 'light',
        'sidenav-color': 'light',
        'sidenav-size': 'default',
        'sidenav-user': true,
        position: 'fixed',
        width: 'fluid',
        dir: 'ltr',
    };

    function readScheme() {
        return localStorage.getItem(SCHEME_KEY) === 'dark' ? 'dark' : 'light';
    }

    function resolveSidenavSize() {
        let size = fixedLayout['sidenav-size'];

        if (window.innerWidth <= 767) {
            return 'offcanvas';
        }

        if (window.innerWidth <= 1140 && size !== 'offcanvas') {
            return 'condensed';
        }

        return size;
    }

    function applyLayout() {
        const scheme = readScheme();

        html.setAttribute('data-skin', fixedLayout.skin);
        html.setAttribute('data-bs-theme', scheme);
        html.setAttribute('data-menu-color', fixedLayout['sidenav-color']);
        html.setAttribute('data-topbar-color', fixedLayout['topbar-color']);
        html.setAttribute('data-layout-width', fixedLayout.width);
        html.setAttribute('data-layout-position', fixedLayout.position);
        html.setAttribute('data-sidenav-size', resolveSidenavSize());
        html.setAttribute('dir', fixedLayout.dir);

        if (fixedLayout['sidenav-user']) {
            html.setAttribute('data-sidenav-user', 'true');
        } else {
            html.removeAttribute('data-sidenav-user');
        }

        window.config = {
            ...fixedLayout,
            theme: scheme,
            'sidenav-size': html.getAttribute('data-sidenav-size'),
        };
        window.defaultConfig = structuredClone(window.config);
    }

    try {
        sessionStorage.removeItem('__THEME_CONFIG__');
    } catch {
        // ignore
    }

    applyLayout();

    window.__dashThemeToggle = function () {
        const next = readScheme() === 'dark' ? 'light' : 'dark';
        localStorage.setItem(SCHEME_KEY, next);
        applyLayout();
        document.dispatchEvent(new CustomEvent('dash-theme-changed', { detail: { scheme: next } }));
    };

    window.__dashThemeScheme = readScheme;

    let resizeTimer = null;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(applyLayout, 150);
    });
})();
