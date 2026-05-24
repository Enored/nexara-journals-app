import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/dashboard-bridge.css',
                'resources/js/dashboard-theme.js',
                'resources/js/dashboard.js',
            ],
            refresh: true,
        }),
    ],
});
