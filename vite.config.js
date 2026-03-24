import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/public.css',
                'resources/css/admin.css',
                'resources/css/lms.css',
                'resources/js/app.js',
                'resources/js/public.js',
                'resources/js/admin.js',
                'resources/js/lms.js',
                'resources/js/lms-progress.js',
                'resources/js/lms-alerts.js'
            ],
            refresh: true,
        }),
    ],
});
