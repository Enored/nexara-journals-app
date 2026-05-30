import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        cors: {
            // Allow the apex AND any journal subdomain (e.g. test.lvh.me) on any
            // port to load dev assets. Without this, visiting a journal subdomain
            // blocks the Vite client/scripts via CORS and the page renders blank.
            origin: [
                /^http:\/\/([a-z0-9-]+\.)*lvh\.me(:\d+)?$/,
                /^http:\/\/(localhost|127\.0\.0\.1)(:\d+)?$/,
            ],
        },
        hmr: {
            host: 'demo.lvh.me',
        },
    },
    plugins: [
        react(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/dashboard-bridge.css',
                'resources/js/dashboard-theme.js',
                'resources/js/dashboard.js',
                'resources/js/inertia.jsx',
            ],
            ssr: 'resources/js/ssr.jsx',
            refresh: true,
        }),
    ],
});
