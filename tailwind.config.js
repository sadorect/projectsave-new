import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#fff1f2',
                    100: '#ffe4e6',
                    200: '#fecdd3',
                    300: '#fda4af',
                    400: '#fb7185',
                    500: '#e11d48',
                    600: '#be123c',
                    700: '#9f1239',
                    800: '#881337',
                    900: '#4c0519',
                },
                accent: {
                    50: '#f5f5f5',
                    100: '#e5e5e5',
                    200: '#d4d4d4',
                    300: '#a3a3a3',
                    400: '#737373',
                    500: '#525252',
                    600: '#404040',
                    700: '#262626',
                    800: '#171717',
                    900: '#0a0a0a',
                },
                ink: {
                    50: '#fafafa',
                    100: '#f4f4f5',
                    200: '#e4e4e7',
                    300: '#d4d4d8',
                    400: '#a1a1aa',
                    500: '#71717a',
                    600: '#52525b',
                    700: '#3f3f46',
                    800: '#27272a',
                    900: '#111111',
                },
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                display: ['Fraunces', ...defaultTheme.fontFamily.serif],
            },
            borderRadius: {
                panel: '1.75rem',
            },
            boxShadow: {
                soft: '0 24px 60px -28px rgba(15, 23, 42, 0.35)',
                panel: '0 18px 45px -26px rgba(30, 41, 59, 0.32)',
            },
        },
    },

    plugins: [forms],
};
