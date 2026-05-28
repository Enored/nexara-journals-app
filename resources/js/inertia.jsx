import React from 'react';
import { createRoot, hydrateRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.jsx', { eager: false });
        return pages[`./Pages/${name}.jsx`]();
    },
    setup({ el, App, props }) {
        if (el.dataset.serverRendered === 'true') {
            hydrateRoot(el, <App {...props} />);
        } else {
            createRoot(el).render(<App {...props} />);
        }
    },
    progress: {
        color: '#0b1a36',
    },
});
