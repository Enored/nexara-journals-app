import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        cors: {
            origin: [
                'http://demo.lvh.me:8000',
                'http://lvh.me:8000',
                'http://localhost:8000',
                'http://127.0.0.1:8000',
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
                'resources/css/journal-home.css',
                'resources/js/journal-home.jsx',
            ],
            refresh: true,
        }),
    ],
});
