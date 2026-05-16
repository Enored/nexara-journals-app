import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                journal: {
                    primary: 'var(--journal-primary, #0f766e)',
                },
                dash: {
                    sidebar: 'rgb(var(--dashboard-sidebar) / <alpha-value>)',
                },
            },
            boxShadow: {
                sidebar: '4px 0 24px -4px rgb(15 23 42 / 0.08)',
            },
        },
    },
    plugins: [],
};
